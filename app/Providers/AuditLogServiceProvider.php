<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Article;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseExam;
use App\Models\CourseRating;
use App\Models\CourseSession;
use App\Models\Evaluation;
use App\Models\Form;
use App\Models\Instructor;
use App\Models\JobTitle;
use App\Models\LmsResource;
use App\Models\Partner;
use App\Models\PublicNotification;
use App\Models\QualificationSkill;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserExam;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Throwable;

/**
 * AuditLogServiceProvider
 *
 * Strictly additive — registers a wildcard listener for every Eloquent
 * model event AND for the Auth Login/Logout events.  Whenever any
 * auditable model is created / updated / deleted (or a user signs in
 * or out), a structured row is written to `audit_logs`, which in turn
 * powers the 2026 admin Audit Log screen.
 *
 * No legacy controller, service, or model is touched.  The provider
 * is registered in bootstrap/providers.php — the only additive
 * configuration change required.
 *
 * Design notes:
 *
 *  • A curated allow-list keeps low-signal models (pivots, lecture
 *    progress, answer rows, etc.) out of the trail.
 *  • The verb is derived from the diff so we get "course → published"
 *    instead of the generic "course → updated" when a publish toggle
 *    flips.
 *  • The actor is resolved by scanning every configured guard for a
 *    signed-in user; admins, instructors, and learners are each
 *    badged correctly.
 *  • A re-entrancy guard prevents the listener from infinite-looping
 *    when the audit row itself is inserted (`AuditLog::created`).
 */
class AuditLogServiceProvider extends ServiceProvider
{
    /**
     * Models we want to surface in the audit trail, keyed by class and
     * mapped to the entity token displayed by the Audit Log table
     * (the `entity →` half of the chip).
     *
     * @var array<class-string,string>
     */
    private const AUDITED_MODELS = [
        User::class                  => 'user',
        Admin::class                 => 'admin',
        Instructor::class            => 'instructor',
        Course::class                => 'course',
        CourseSession::class         => 'session',
        CourseAssignment::class      => 'assignment',
        UserCourseAssignment::class  => 'submission',
        CourseExam::class            => 'quiz',
        UserExam::class              => 'attempt',
        Category::class              => 'category',
        JobTitle::class              => 'job_title',
        QualificationSkill::class    => 'qualification',
        Article::class               => 'article',
        LmsResource::class           => 'resource',
        PublicNotification::class    => 'notification',
        Setting::class               => 'settings',
        CourseRating::class          => 'rating',
        Evaluation::class            => 'evaluation',
        Form::class                  => 'form',
        Testimonial::class           => 'testimonial',
        Partner::class               => 'partner',
    ];

    /**
     * Attribute name candidates we probe for a row's human label,
     * in priority order.  The first one we find on the model is used.
     *
     * @var array<int,string>
     */
    private const LABEL_ATTRIBUTES = [
        'title', 'name', 'subject', 'label', 'topic', 'description', 'message', 'key', 'code',
    ];

    /**
     * Verbs unlocked when a specific attribute changes in a particular
     * way during an `updated` event. Keys are the watched attribute,
     * each value yields a {from,to => verb} table.
     *
     * @var array<string,array<string,string>>
     */
    private const STATE_VERBS = [
        'status' => [
            'published'   => 'published',
            'unpublished' => 'unpublished',
            'archived'    => 'archived',
            'cancelled'   => 'cancelled',
            'completed'   => 'completed',
            'started'     => 'started',
            'sent'        => 'sent',
            'graded'      => 'graded',
            'approved'    => 'approved',
            'rejected'    => 'rejected',
            'active'      => 'activated',
            'inactive'    => 'deactivated',
            'deactivated' => 'deactivated',
        ],
    ];

    /**
     * Boolean column → (was false / now true) verbs.
     *
     * @var array<string,string>
     */
    private const BOOL_VERBS = [
        'is_published' => 'published',
        'published'    => 'published',
        'is_active'    => 'activated',
        'active'       => 'activated',
        'is_locked'    => 'locked',
    ];

    /** Re-entrancy guard so audit inserts don't recurse. */
    private bool $writing = false;

    public function boot(): void
    {
        // Defensive: never log before the table exists (fresh installs).
        try {
            if (!Schema::hasTable('audit_logs')) {
                return;
            }
        } catch (Throwable) {
            return;
        }

        $this->registerEloquentListeners();
        $this->registerAuthListeners();
    }

    /* ------------------------------------------------------------------ *
     |  Eloquent listeners                                                |
     * ------------------------------------------------------------------ */

    private function registerEloquentListeners(): void
    {
        foreach (['created', 'updated', 'deleted', 'restored'] as $hook) {
            Event::listen("eloquent.{$hook}: *", function (string $event, array $payload) use ($hook): void {
                $model = $payload[0] ?? null;
                if ($model instanceof Model) {
                    $this->onModelEvent($hook, $model);
                }
            });
        }
    }

    /**
     * Persist an audit row for an eligible Eloquent event.
     */
    private function onModelEvent(string $hook, Model $model): void
    {
        if ($this->writing) {
            return;
        }
        if ($model instanceof AuditLog) {
            return;
        }

        $entity = $this->entityTokenFor($model);
        if ($entity === null) {
            return;
        }

        $verb = $this->verbFor($hook, $model);
        if ($verb === null) {
            // `updated` was called but nothing meaningful changed.
            return;
        }

        $actor = $this->currentActor();
        $role  = $this->roleFor($actor);

        // Skip noise: anonymous traffic, migrations, seeders, queue
        // workers — anything without an authenticated actor.
        if ($actor === null) {
            return;
        }

        $this->write([
            'user_type'   => $this->guessUserType($actor),
            'user_id'     => $actor?->getKey(),
            'user_name'   => $this->actorDisplayName($actor),
            'actor_role'  => $role,
            'action'      => $verb,
            'model_type'  => get_class($model),
            'model_id'    => $model->getKey(),
            'description' => $this->describe($model),
            'ip_address'  => $this->clientIp(),
        ]);
    }

    /* ------------------------------------------------------------------ *
     |  Auth listeners                                                    |
     * ------------------------------------------------------------------ */

    private function registerAuthListeners(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            $this->onAuthEvent($event->user, 'logged_in');
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if (!$event->user) {
                return;
            }
            $this->onAuthEvent($event->user, 'logged_out');
        });

        Event::listen(Failed::class, function (Failed $event): void {
            // Failed login attempts are valuable but the actor is unknown —
            // we log them as a system event for traceability.
            if ($this->writing) {
                return;
            }
            $email = is_array($event->credentials) ? ($event->credentials['email'] ?? null) : null;
            $this->write([
                'user_type'   => 'system',
                'user_id'     => null,
                'user_name'   => $email ? "Login attempt: {$email}" : 'Unknown login attempt',
                'actor_role'  => 'system',
                'action'      => 'login_failed',
                'model_type'  => null,
                'model_id'    => null,
                'description' => $email ? "Failed login for {$email}" : 'Failed login attempt',
                'ip_address'  => $this->clientIp(),
            ]);
        });
    }

    private function onAuthEvent(?\Illuminate\Contracts\Auth\Authenticatable $actor, string $verb): void
    {
        if ($this->writing || !$actor instanceof Model) {
            return;
        }

        $role = $this->roleFor($actor);

        $this->write([
            'user_type'   => $this->guessUserType($actor),
            'user_id'     => $actor->getKey(),
            'user_name'   => $this->actorDisplayName($actor),
            'actor_role'  => $role,
            'action'      => $verb,
            'model_type'  => get_class($actor),
            'model_id'    => $actor->getKey(),
            'description' => sprintf('%s session', ucfirst(str_replace('_', ' ', $verb))),
            'ip_address'  => $this->clientIp(),
        ]);
    }

    /* ------------------------------------------------------------------ *
     |  Persistence                                                       |
     * ------------------------------------------------------------------ */

    /**
     * @param  array<string,mixed>  $row
     */
    private function write(array $row): void
    {
        $this->writing = true;
        try {
            // forceFill() bypasses $fillable so we can persist the
            // additive `actor_role` column without modifying the
            // legacy AuditLog model.
            (new AuditLog())->forceFill($row)->save();
        } catch (Throwable) {
            // Swallow — auditing must never break the underlying request.
        } finally {
            $this->writing = false;
        }
    }

    /* ------------------------------------------------------------------ *
     |  Model helpers                                                     |
     * ------------------------------------------------------------------ */

    private function entityTokenFor(Model $model): ?string
    {
        return self::AUDITED_MODELS[get_class($model)] ?? null;
    }

    /**
     * Compute the verb for a given hook. Returns null if `updated`
     * was fired but no meaningful change happened.
     */
    private function verbFor(string $hook, Model $model): ?string
    {
        if ($hook === 'created')  return 'created';
        if ($hook === 'deleted')  return 'deleted';
        if ($hook === 'restored') return 'restored';

        // hook === 'updated' — sniff the diff for a richer verb.
        $changes = $model->getChanges();
        if (empty($changes)) {
            return null;
        }

        foreach ($changes as $attr => $newValue) {
            if (isset(self::BOOL_VERBS[$attr])) {
                $cast = filter_var($newValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($cast === true) {
                    return self::BOOL_VERBS[$attr];
                }
            }
        }

        foreach (self::STATE_VERBS as $attr => $map) {
            if (!array_key_exists($attr, $changes)) {
                continue;
            }
            $newValue = strtolower((string) $changes[$attr]);
            if (isset($map[$newValue])) {
                return $map[$newValue];
            }
        }

        return 'updated';
    }

    /**
     * Produce a short, human-readable label for a model row.
     */
    private function describe(Model $model): string
    {
        foreach (self::LABEL_ATTRIBUTES as $attr) {
            if (!array_key_exists($attr, $model->getAttributes())) {
                continue;
            }
            $value = $model->getAttribute($attr);
            $value = $this->stringifyAttribute($value);
            if ($value !== '') {
                return Str::limit($value, 180);
            }
        }

        // Fall back to "<EntityName> #<id>".
        $basename = class_basename($model);
        return $model->getKey() !== null
            ? "{$basename} #{$model->getKey()}"
            : $basename;
    }

    /**
     * Coerce an attribute that may be a Spatie translation array,
     * JSON string, or scalar into a clean display string.
     */
    private function stringifyAttribute(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_array($value)) {
            $en = $value['en'] ?? null;
            $ar = $value['ar'] ?? null;
            return (string) ($en ?: ($ar ?: reset($value) ?: ''));
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $this->stringifyAttribute($decoded);
            }
            return trim($value);
        }
        return (string) $value;
    }

    /* ------------------------------------------------------------------ *
     |  Actor resolution                                                  |
     * ------------------------------------------------------------------ */

    /**
     * Walk every configured guard and return the first authenticated
     * user we find.  We don't rely on the default guard alone because
     * the API exposes both an admin guard and a user guard.
     */
    private function currentActor(): ?Model
    {
        try {
            $direct = Auth::user();
            if ($direct instanceof Model) {
                return $direct;
            }
        } catch (Throwable) {
            // ignore
        }

        $guards = array_keys(config('auth.guards', []) ?: []);
        foreach ($guards as $guard) {
            try {
                $user = Auth::guard($guard)->user();
                if ($user instanceof Model) {
                    return $user;
                }
            } catch (Throwable) {
                continue;
            }
        }

        return null;
    }

    private function roleFor(?\Illuminate\Contracts\Auth\Authenticatable $actor): string
    {
        if ($actor === null)                   return 'system';
        if ($actor instanceof Admin)           return 'admin';
        if ($actor instanceof Instructor)      return 'instructor';
        return 'learner';
    }

    private function guessUserType(?\Illuminate\Contracts\Auth\Authenticatable $actor): string
    {
        if ($actor === null)              return 'system';
        if ($actor instanceof Admin)      return 'admin';
        return 'user';
    }

    private function actorDisplayName(?\Illuminate\Contracts\Auth\Authenticatable $actor): string
    {
        if ($actor === null) {
            return 'System';
        }

        // Try the model's `name` first, falling back to Spatie's
        // translatable getter for instructor rows.
        $raw = $actor->getAttribute('name');
        $name = $this->stringifyAttribute($raw);
        if ($name !== '') {
            return $name;
        }

        return method_exists($actor, 'getAuthIdentifier')
            ? sprintf('%s #%s', class_basename($actor), $actor->getAuthIdentifier())
            : 'Unknown';
    }

    /* ------------------------------------------------------------------ *
     |  Request helpers                                                   |
     * ------------------------------------------------------------------ */

    private function clientIp(): ?string
    {
        try {
            return request()?->ip();
        } catch (Throwable) {
            return null;
        }
    }
}
