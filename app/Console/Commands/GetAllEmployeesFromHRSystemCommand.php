<?php

namespace App\Console\Commands;

use App\Models\JobTitle;
use App\Models\User;
use App\Services\HRSystemService;
use App\Services\JobTitleSyncService;
use Illuminate\Console\Command;

class GetAllEmployeesFromHRSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get All Employees From HR System';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hrService = new HRSystemService();
        $page = 0;
        $batchSize = 10;

        // Fetch all employees in both languages up-front so we can
        // populate name_ar / name_en in a single upsert pass.
        $employeesAr = $hrService->getAllEmployees('ar');
        $employeesEn = $hrService->getAllEmployees('en');

        // Build a quick id → English-name lookup.
        $enNamesById = $employeesEn
            ->filter(fn ($e) => is_object($e) && isset($e->id))
            ->mapWithKeys(fn ($e) => [(string) $e->id => (string) ($e->name ?? '')])
            ->all();

        // Distinct jobName → job_titles.id lookup cache so we don't
        // re-query the catalogue for every employee in a batch.
        $jobTitleCache = [];
        $resolveTitleId = static function (?string $jobName) use (&$jobTitleCache): ?int {
            $jobName = $jobName !== null ? trim($jobName) : '';
            if ($jobName === '') {
                return null;
            }
            if (array_key_exists($jobName, $jobTitleCache)) {
                return $jobTitleCache[$jobName];
            }
            $row = JobTitle::firstOrCreate(['name' => $jobName]);
            return $jobTitleCache[$jobName] = $row->id;
        };

        while (true) {
            if ($employeesAr->isEmpty()) {
                $this->info('no employees');
                break;
            }
            // Slice the next batch
            $batch = $employeesAr->slice($page * $batchSize, $batchSize);
            if ($batch->isEmpty()) {
                break;
            }
            foreach ($batch as $employee) {
                $nameAr = (string) ($employee->name ?? '');
                $nameEn = $enNamesById[(string) ($employee->id ?? '')] ?? null;

                // `users.job_title` (string) was dropped by the 2026
                // admin Users redesign; the link is now the
                // `users.job_title_id` FK seeded here from HR's
                // `jobName` field.
                User::updateOrCreate(
                    ['system_id' => $employee->id], // unique key
                    [
                        'name'            => $nameAr,
                        'name_ar'         => $nameAr ?: null,
                        'name_en'         => $nameEn ?: null,
                        'email'           => $employee->email,
                        'phone'           => $employee->phone,
                        'machine_code'    => $employee->machineCode,
                        'department_name' => $employee->departmentName,
                        'job_title_id'    => $resolveTitleId($employee->jobName ?? null),
                        'updated_at'      => now(),
                    ]
                );
            }

            $this->info("Processed batch " . ($page + 1));
            $page++;
        }

        // A fresh HR pull may have introduced new jobs — keep the Job
        // Titles catalogue in sync so the admin screen lights up
        // without a separate manual command. Uses the HR /Job endpoint
        // and filters by employees > 0.
        $report = app(JobTitleSyncService::class)->syncFromHr();
        $this->info(sprintf(
            'Job-titles catalogue: %d HR jobs, %d eligible (employees > 0), %d created, %d unchanged, %d orphaned.',
            $report['source_rows'],
            $report['eligible'],
            $report['created'],
            $report['unchanged'],
            $report['orphaned'],
        ));
    }
}
