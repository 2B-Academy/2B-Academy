<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * php artisan cohorts:sync-statuses
 *
 * Roll the calendar forward across every cohort: a `scheduled` cohort
 * becomes `active` the day its `start_date` arrives, and an `active`
 * cohort becomes `completed` the day after its `end_date` passes. The
 * parent course's `active` flag then tracks whether at least one
 * cohort is currently within its date window.
 *
 * Read time already derives these on the fly via Course::effectiveStatus,
 * so the UI is always live — this command exists so the persisted
 * `course_sections.status` and `courses.active` columns agree with
 * what users see (matters for raw queries, reports, and any consumer
 * that bypasses the resources).
 *
 * Manual overrides win:
 *   - `course_sections.status = 'inactive'` is never auto-transitioned.
 *   - A course with no cohorts keeps its stored `active` flag intact.
 *
 * Idempotent + race-safe — re-running mid-day is always safe.
 */
class SyncCohortStatusesCommand extends Command
{
    protected $signature = 'cohorts:sync-statuses
        {--dry-run : Show the planned writes without touching the database}';

    protected $description = 'Roll cohort + course status forward based on the calendar (run daily).';

    public function handle(): int
    {
        $today  = Carbon::today();
        $dryRun = (bool) $this->option('dry-run');

        $sectionUpdates = 0;
        $courseUpdates  = 0;

        // ── 1. Update cohort statuses ────────────────────────────────
        CourseSection::query()
            ->select(['id', 'course_id', 'start_date', 'end_date', 'status'])
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->orderBy('id')
            ->chunkById(500, function ($chunk) use ($today, $dryRun, &$sectionUpdates) {
                foreach ($chunk as $section) {
                    $next = Course::deriveCohortStatus(
                        $section->status,
                        $section->start_date instanceof Carbon
                            ? $section->start_date
                            : Carbon::parse($section->start_date),
                        $section->end_date instanceof Carbon
                            ? $section->end_date
                            : Carbon::parse($section->end_date),
                    );

                    if ($next === $section->status) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line(sprintf(
                            '  [dry-run] cohort #%d (%s → %s)',
                            $section->id,
                            $section->status ?? 'null',
                            $next,
                        ));
                    } else {
                        DB::table('course_sections')
                            ->where('id', $section->id)
                            ->update(['status' => $next, 'updated_at' => $today]);
                    }
                    $sectionUpdates++;
                }
            });

        // ── 2. Roll course active flag up from cohort status ────────
        Course::query()
            ->select(['id', 'active'])
            ->orderBy('id')
            ->chunkById(500, function ($chunk) use ($today, $dryRun, &$courseUpdates) {
                $courseIds = $chunk->pluck('id')->all();
                if (empty($courseIds)) {
                    return;
                }

                // Pull every cohort for the chunk in one query so we
                // can derive each course's effective status without
                // tripping N+1s.
                $sectionsByCourse = CourseSection::query()
                    ->whereIn('course_id', $courseIds)
                    ->get(['id', 'course_id', 'start_date', 'end_date', 'status'])
                    ->groupBy('course_id');

                foreach ($chunk as $course) {
                    $sections = $sectionsByCourse->get($course->id, collect());

                    if ($sections->isEmpty()) {
                        // No cohorts → leave the stored flag alone so
                        // admins can stage courses without them flipping
                        // back to inactive overnight.
                        continue;
                    }

                    $effectiveActive = false;
                    foreach ($sections as $section) {
                        $start = $section->start_date instanceof Carbon
                            ? $section->start_date
                            : ($section->start_date ? Carbon::parse($section->start_date) : null);
                        $end   = $section->end_date instanceof Carbon
                            ? $section->end_date
                            : ($section->end_date ? Carbon::parse($section->end_date) : null);

                        if (Course::deriveCohortStatus($section->status, $start, $end) === 'active') {
                            $effectiveActive = true;
                            break;
                        }
                    }

                    if ((bool) $course->active === $effectiveActive) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line(sprintf(
                            '  [dry-run] course #%d active (%s → %s)',
                            $course->id,
                            $course->active ? 'true' : 'false',
                            $effectiveActive ? 'true' : 'false',
                        ));
                    } else {
                        DB::table('courses')
                            ->where('id', $course->id)
                            ->update(['active' => $effectiveActive, 'updated_at' => $today]);
                    }
                    $courseUpdates++;
                }
            });

        $this->info(sprintf(
            '%scohorts updated: %d · courses updated: %d',
            $dryRun ? '[dry-run] ' : '',
            $sectionUpdates,
            $courseUpdates,
        ));

        return self::SUCCESS;
    }
}
