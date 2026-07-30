<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Admin;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Instructor;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Two-way messaging for the learner web (Figma frames 841-42746 / 841-43294).
 *
 * A conversation is a thread between polymorphic participants (User /
 * Instructor / Admin). The authenticated learner signs in as a User but may
 * be addressed under any same-email identity (the platform's cross-entity
 * convention — see NotificationInboxController), so the feed is scoped to all
 * of them.
 */
final class MessageService
{
    /** Role buckets for the All / Instructors / Admins tabs. */
    private const ROLE_BY_TYPE = [
        User::class       => 'learners',
        Instructor::class => 'instructors',
        Admin::class      => 'admins',
    ];

    /**
     * The learner's conversations, newest activity first, with counterpart,
     * last-message preview and unread count. Optionally filtered by the
     * counterpart's role ("instructors" | "admins").
     */
    public function conversationsFor(Model $principal, ?string $role, int $perPage, ?string $tab = null): LengthAwarePaginator
    {
        $identities = $this->identities($principal);

        $paginator = Conversation::query()
            ->whereHas('participants', fn ($q) => $this->scopeToIdentities($q, $identities))
            ->with(['latestMessage', 'course:id,title', 'participants'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        $paginator->getCollection()->transform(function (Conversation $c) use ($identities, $role) {
            return $this->shape($c, $identities, $role);
        });

        // Drop rows filtered out by role (shape() ⇒ null) or the dashboard tab
        // (all | unread | received | sent).
        $paginator->setCollection(
            $paginator->getCollection()
                ->filter(fn ($shaped) => $shaped !== null && $this->matchesTab($shaped, $tab))
                ->values()
        );

        return $paginator;
    }

    /** Dashboard inbox tab filter over an already-shaped conversation row. */
    private function matchesTab(array $c, ?string $tab): bool
    {
        return match ($tab) {
            'unread'   => (int) ($c['unread_count'] ?? 0) > 0,
            'received' => ! empty($c['last_message']) && $c['last_message']['mine'] === false,
            'sent'     => ! empty($c['last_message']) && $c['last_message']['mine'] === true,
            default    => true,
        };
    }

    /**
     * Thread messages (oldest first) + mark the conversation read for the
     * learner. Throws if the learner isn't a participant.
     *
     * @return array{conversation: array<string,mixed>, messages: Collection<int, array<string,mixed>>}
     */
    public function thread(Model $principal, Conversation $conversation): array
    {
        $identities = $this->identities($principal);
        $participant = $this->participantRow($conversation, $identities);
        abort_if($participant === null, 403);

        $participant->update(['last_read_at' => now()]);

        $messages = $conversation->messages()
            ->orderBy('id')
            ->get()
            ->map(fn (Message $m) => $this->shapeMessage($m, $identities));

        return [
            'conversation' => $this->shape($conversation->fresh(['latestMessage', 'course:id,title', 'participants']), $identities, null) ?? [],
            'messages'     => $messages,
        ];
    }

    /** Post a reply into an existing conversation the learner belongs to. */
    public function reply(Model $principal, Conversation $conversation, string $body): Message
    {
        $identities = $this->identities($principal);
        abort_if($this->participantRow($conversation, $identities) === null, 403);

        return DB::transaction(function () use ($conversation, $principal, $body) {
            $message = $conversation->messages()->create([
                'sender_type' => $principal::class,
                'sender_id'   => $principal->getKey(),
                'body'        => $body,
            ]);

            $conversation->update(['last_message_at' => now()]);

            return $message;
        });
    }

    /**
     * Start a new conversation with a recipient (instructor/admin) and send
     * the first message. Idempotently reuses an existing 1:1 thread with the
     * same recipient + course so replies stay in one place.
     */
    public function start(Model $principal, string $recipientType, int $recipientId, ?int $courseId, string $body, ?string $subject = null): Message
    {
        abort_unless(in_array($recipientType, [Instructor::class, Admin::class, User::class], true), 422);
        $recipient = $recipientType::query()->find($recipientId);
        abort_if($recipient === null, 404);

        return DB::transaction(function () use ($principal, $recipient, $recipientType, $recipientId, $courseId, $body, $subject) {
            $conversation = $this->findExisting($principal, $recipientType, $recipientId, $courseId)
                ?? Conversation::create(['course_id' => $courseId, 'subject' => $subject, 'last_message_at' => now()]);

            $this->ensureParticipant($conversation, $principal::class, (int) $principal->getKey());
            $this->ensureParticipant($conversation, $recipientType, $recipientId);

            $message = $conversation->messages()->create([
                'sender_type' => $principal::class,
                'sender_id'   => $principal->getKey(),
                'body'        => $body,
            ]);

            $conversation->update(['last_message_at' => now()]);

            return $message;
        });
    }

    /**
     * Fan out one message into an individual conversation per recipient — the
     * dashboard broadcast (to selected learners / whole admin roles) mapped
     * onto the two-way store. Skips self, unknown ids, and duplicates.
     *
     * @param  array<int, array{type: class-string, id: int}>  $recipients
     * @return int  number of conversations messaged
     */
    public function startMany(Model $principal, array $recipients, ?string $subject, string $body): int
    {
        $identities = $this->identities($principal);
        $seen = [];
        $count = 0;

        foreach ($recipients as $r) {
            $type = $r['type'];
            $id   = (int) $r['id'];
            $key  = $type . ':' . $id;

            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            if (! in_array($type, [Instructor::class, Admin::class, User::class], true)) {
                continue;
            }
            if ($this->isMine($type, $id, $identities)) {
                continue; // never message yourself
            }
            if (! $type::query()->whereKey($id)->exists()) {
                continue;
            }

            $this->start($principal, $type, $id, null, $body, $subject);
            $count++;
        }

        return $count;
    }

    /**
     * People the current principal may start a conversation with, grouped:
     *   - a LEARNER (website)  → the instructors of the courses they're
     *     enrolled in (one row per instructor, de-duplicated) + every Super
     *     Admin (dashboard user with the Super Admin role / all permissions).
     *   - an ADMIN / INSTRUCTOR (dashboard) → all learners.
     *
     * Each row carries a `role` bucket (`instructors` | `admins` | `learners`)
     * so the picker can section them.
     *
     * @return array<int, array<string, mixed>>
     */
    public function recipientsFor(Model $principal): array
    {
        $locale = app()->getLocale();

        if ($principal instanceof User) {
            // Instructors of the learner's enrolled courses — one row each.
            $instructors = DB::table('users_courses as uc')
                ->join('courses_instructors as ci', 'ci.course_id', '=', 'uc.course_id')
                ->join('instructors as i', 'i.id', '=', 'ci.instructor_id')
                ->where('uc.user_id', $principal->id)
                ->distinct()
                ->get(['i.id', 'i.name', 'i.image'])
                ->map(fn ($r) => $this->recipientRow('instructor', 'instructors', (int) $r->id, (string) $r->name, $r->image, $locale));

            // Super Admins — dashboard users holding the all-permissions role.
            $superAdmins = Admin::query()
                ->whereHas('roles', fn ($q) => $q->whereIn(DB::raw('LOWER(name)'), ['superadmin', 'super-admin', 'super_admin']))
                ->get()
                ->map(fn (Admin $a) => $this->recipientRow('admin', 'admins', (int) $a->id, (string) ($a->name ?? ''), $a->image, $locale));

            return $instructors->concat($superAdmins)->unique(fn ($r) => $r['recipient_type'] . ':' . $r['recipient_id'])->values()->all();
        }

        if ($principal instanceof Admin || $principal instanceof Instructor) {
            // Dashboard compose → learners.
            return User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'image'])
                ->map(fn (User $u) => $this->recipientRow('learner', 'learners', (int) $u->id, (string) ($u->name ?? ''), $u->image, $locale))
                ->all();
        }

        return [];
    }

    /** @return array<string, mixed> */
    private function recipientRow(string $type, string $group, int $id, string $name, $image, string $locale): array
    {
        return [
            'recipient_type' => $type,   // instructor | admin | learner
            'recipient_id'   => $id,
            'name'           => $this->localize($name, $locale) ?: '—',
            'image'          => $image
                ? (str_starts_with((string) $image, 'http') ? $image : asset('storage/' . ltrim((string) $image, '/')))
                : null,
            'role'           => $group,  // grouping bucket for the picker
        ];
    }

    private function localize(string $json, string $locale): string
    {
        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            return (string) ($decoded[$locale] ?? $decoded['en'] ?? $decoded['ar'] ?? '');
        }

        return $json;
    }

    /* ────────────────────────────────────────────────────────────── */

    /** @param array<int, array{0:class-string,1:int}> $identities */
    private function shape(Conversation $conversation, array $identities, ?string $roleFilter): ?array
    {
        $mine = $this->participantRow($conversation, $identities);

        // The counterpart = the first participant that isn't one of my identities.
        $counterpartRow = $conversation->participants->first(
            fn (ConversationParticipant $p) => ! $this->isMine($p->participant_type, (int) $p->participant_id, $identities),
        );

        $role = $counterpartRow ? (self::ROLE_BY_TYPE[$counterpartRow->participant_type] ?? 'learners') : 'learners';
        if ($roleFilter !== null && $roleFilter !== 'all' && $role !== $roleFilter) {
            return null;
        }

        $counterpart = $counterpartRow ? $this->resolveIdentity($counterpartRow->participant_type, (int) $counterpartRow->participant_id) : null;
        $last = $conversation->latestMessage;

        $unread = $conversation->messages()
            ->when($mine?->last_read_at, fn ($q) => $q->where('created_at', '>', $mine->last_read_at))
            ->where(fn ($q) => $this->scopeSenderNotMine($q, $identities))
            ->count();

        return [
            'id'         => (int) $conversation->id,
            'subject'    => $conversation->subject,
            'course'     => $conversation->course ? [
                'id'    => (int) $conversation->course->id,
                'title' => $conversation->course->getTranslation('title', app()->getLocale()),
            ] : null,
            'counterpart' => [
                'name'  => $counterpart['name'] ?? '—',
                'image' => $counterpart['image'] ?? null,
                'role'  => $role,
            ],
            'last_message' => $last ? [
                'body'       => \Illuminate\Support\Str::limit($last->body, 90),
                'created_at' => optional($last->created_at)->toIso8601String(),
                'mine'       => $this->isMine($last->sender_type, (int) $last->sender_id, $identities),
            ] : null,
            'unread_count'    => $unread,
            'last_message_at' => optional($conversation->last_message_at)->toIso8601String(),
        ];
    }

    /** @param array<int, array{0:class-string,1:int}> $identities */
    private function shapeMessage(Message $m, array $identities): array
    {
        $sender = $this->resolveIdentity($m->sender_type, (int) $m->sender_id);

        return [
            'id'          => (int) $m->id,
            'body'        => $m->body,
            'mine'        => $this->isMine($m->sender_type, (int) $m->sender_id, $identities),
            'sender_name' => $sender['name'] ?? '—',
            'created_at'  => optional($m->created_at)->toIso8601String(),
        ];
    }

    /** @return array<int, array{0:class-string,1:int}> */
    private function identities(Model $principal): array
    {
        $identities = [[$principal::class, (int) $principal->getKey()]];

        $email = $principal->email ?? null;
        if ($email) {
            foreach ([User::class, Instructor::class, Admin::class] as $model) {
                foreach ($model::query()->where('email', $email)->pluck('id') as $id) {
                    $identities[] = [$model, (int) $id];
                }
            }
        }

        return array_values(array_unique($identities, SORT_REGULAR));
    }

    /** @param array<int, array{0:class-string,1:int}> $identities */
    private function scopeToIdentities($query, array $identities): void
    {
        $query->where(function ($q) use ($identities) {
            foreach ($identities as [$type, $id]) {
                $q->orWhere(fn ($inner) => $inner->where('participant_type', $type)->where('participant_id', $id));
            }
        });
    }

    /** @param array<int, array{0:class-string,1:int}> $identities */
    private function scopeSenderNotMine($query, array $identities): void
    {
        foreach ($identities as [$type, $id]) {
            $query->whereNot(fn ($q) => $q->where('sender_type', $type)->where('sender_id', $id));
        }
    }

    /** @param array<int, array{0:class-string,1:int}> $identities */
    private function participantRow(Conversation $conversation, array $identities): ?ConversationParticipant
    {
        return $conversation->participants->first(
            fn (ConversationParticipant $p) => $this->isMine($p->participant_type, (int) $p->participant_id, $identities),
        ) ?? ConversationParticipant::where('conversation_id', $conversation->id)
            ->where(fn ($q) => $this->scopeToIdentities($q, $identities))
            ->first();
    }

    /** @param array<int, array{0:class-string,1:int}> $identities */
    private function isMine(string $type, int $id, array $identities): bool
    {
        foreach ($identities as [$t, $i]) {
            if ($t === $type && $i === $id) {
                return true;
            }
        }

        return false;
    }

    private function ensureParticipant(Conversation $conversation, string $type, int $id): void
    {
        ConversationParticipant::firstOrCreate([
            'conversation_id'  => $conversation->id,
            'participant_type' => $type,
            'participant_id'   => $id,
        ]);
    }

    private function findExisting(Model $principal, string $recipientType, int $recipientId, ?int $courseId): ?Conversation
    {
        return Conversation::query()
            ->where('course_id', $courseId)
            ->whereHas('participants', fn ($q) => $q->where('participant_type', $principal::class)->where('participant_id', $principal->getKey()))
            ->whereHas('participants', fn ($q) => $q->where('participant_type', $recipientType)->where('participant_id', $recipientId))
            ->first();
    }

    /** @return array{name:?string, image:?string}|null */
    private function resolveIdentity(string $type, int $id): ?array
    {
        /** @var Model|null $model */
        $model = $type::query()->find($id);
        if ($model === null) {
            return null;
        }

        // Instructor (and some other) `name` fields are translatable JSON —
        // localize so the UI never shows a raw {"en":..,"ar":..} blob.
        $name = $model->name ?? null;

        return [
            'name'  => $name !== null ? $this->localize((string) $name, app()->getLocale()) : null,
            'image' => isset($model->image) && $model->image
                ? (str_starts_with((string) $model->image, 'http') ? $model->image : asset('storage/' . ltrim((string) $model->image, '/')))
                : null,
        ];
    }
}
