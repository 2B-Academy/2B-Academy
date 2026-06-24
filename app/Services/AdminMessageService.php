<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminMessage;
use App\Models\AdminMessageRecipient;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminMessageService
{
    /**
     * Permission keys that mark a role as having "Learning Operations"
     * access. Any admin-guard role holding at least one of these becomes
     * a selectable recipient group in the New Message dialog (Figma).
     *
     * @var array<int, string>
     */
    public const LEARNING_OPS_PERMISSIONS = [
        'view-courses',
        'view-assignments',
        'view-quizzes',
        'view-ratings',
        'view-resources',
    ];

    /**
     * Recipient catalog for the compose dialog — the "Learners" group
     * (website users) plus one group per admin-guard role that has
     * Learning-Operations access, each carrying its selectable members.
     *
     * @return array<int, array<string, mixed>>
     */
    public function recipientCatalog(): array
    {
        $locale = app()->getLocale();
        $groups = [];

        // ── Learners (website users) ──────────────────────────────────
        $learners = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'name_en', 'name_ar'])
            ->map(fn (User $u) => [
                'id'   => (int) $u->id,
                'name' => $u->getLocalizedName(),
            ])
            ->values()
            ->all();

        $groups[] = [
            'key'     => 'learner',
            'type'    => 'learner',
            'role_id' => null,
            'label'   => __('messages.inbox.learners'),
            'members' => $learners,
        ];

        // ── Admin-guard roles with Learning-Operations access ─────────
        $roleIds = DB::table('roles')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('roles.guard_name', 'admin')
            ->where('permissions.guard_name', 'admin')
            ->whereIn('permissions.name', self::LEARNING_OPS_PERMISSIONS)
            ->distinct()
            ->pluck('roles.id')
            ->all();

        if (! empty($roleIds)) {
            $roles = DB::table('roles')
                ->whereIn('id', $roleIds)
                ->where('guard_name', 'admin')
                ->orderByDesc('is_system')
                ->orderBy('name_en')
                ->get();

            $morph = (new Admin())->getMorphClass();

            foreach ($roles as $role) {
                $adminIds = DB::table('model_has_roles')
                    ->where('role_id', $role->id)
                    ->where('model_type', $morph)
                    ->pluck('model_id')
                    ->all();

                $members = empty($adminIds) ? [] : Admin::query()
                    ->whereIn('id', $adminIds)
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (Admin $a) => [
                        'id'   => (int) $a->id,
                        'name' => $a->name,
                    ])
                    ->values()
                    ->all();

                $groups[] = [
                    'key'     => 'role:' . $role->id,
                    'type'    => 'role',
                    'role_id' => (int) $role->id,
                    'label'   => $this->roleLabel($role, $locale),
                    'members' => $members,
                ];
            }
        }

        return $groups;
    }

    public function list(
        int     $perPage  = 15,
        ?string $search   = null,
        ?string $tab      = 'received',
        ?int    $adminId  = null,
        ?int    $userId   = null,
    ): LengthAwarePaginator {
        $morph = (new Admin())->getMorphClass();

        return AdminMessage::query()
            ->with('admin:id,name')
            ->withCount([
                'recipients as total_recipients',
                'recipients as read_count'                  => fn ($q) => $q->whereNotNull('read_at'),
                'recipients as learner_recipients_count'    => fn ($q) => $q->whereNotNull('user_id'),
                'recipients as instructor_recipients_count' => fn ($q) => $q->whereNotNull('instructor_id'),
                'recipients as admin_recipients_count'      => fn ($q) => $q->whereNotNull('admin_id'),
                // Viewer-specific: how many of MY recipient rows are still unread.
                'recipients as my_unread_count' => fn ($q) => $q
                    ->whereNull('read_at')
                    ->when($adminId, fn ($r) => $r->where('admin_id', $adminId))
                    ->when(! $adminId && $userId, fn ($r) => $r->where('user_id', $userId)),
            ])
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('subject', 'LIKE', "%{$search}%")
                   ->orWhere('body', 'LIKE', "%{$search}%");
            }))
            // ── Tab filtering (viewer = the logged-in back-office admin) ──
            ->when($tab === 'sent' && $adminId, fn ($q) => $q->where('admin_id', $adminId))
            ->when($tab === 'received', fn ($q) => $q->whereHas('recipients', function ($r) use ($adminId, $userId) {
                $r->when($adminId, fn ($x) => $x->where('admin_id', $adminId))
                  ->when(! $adminId && $userId, fn ($x) => $x->where('user_id', $userId));
            }))
            ->when($tab === 'unread', fn ($q) => $q->whereHas('recipients', function ($r) use ($adminId, $userId) {
                $r->whereNull('read_at')
                  ->when($adminId, fn ($x) => $x->where('admin_id', $adminId))
                  ->when(! $adminId && $userId, fn ($x) => $x->where('user_id', $userId));
            }))
            ->latest()
            ->paginate($perPage);
    }

    public function show(AdminMessage $message): AdminMessage
    {
        return $message->load([
            'admin:id,name',
            'recipients.user:id,name,name_en,name_ar',
            'recipients.instructor:id,name',
            'recipients.admin:id,name',
        ])->loadCount([
            'recipients as total_recipients',
            'recipients as read_count'                  => fn ($q) => $q->whereNotNull('read_at'),
            'recipients as learner_recipients_count'    => fn ($q) => $q->whereNotNull('user_id'),
            'recipients as instructor_recipients_count' => fn ($q) => $q->whereNotNull('instructor_id'),
            'recipients as admin_recipients_count'      => fn ($q) => $q->whereNotNull('admin_id'),
        ]);
    }

    /**
     * Create a message and fan it out to every resolved recipient.
     *
     * @param  array{subject:string, body:string, groups:array<int, array<string, mixed>>}  $data
     */
    public function create(array $data, int $adminId): AdminMessage
    {
        return DB::transaction(function () use ($data, $adminId) {
            [$userIds, $adminIds, $audience] = $this->resolveGroups($data['groups'] ?? []);

            /** @var AdminMessage $message */
            $message = AdminMessage::create([
                'admin_id' => $adminId,
                'subject'  => $data['subject'],
                'body'     => $data['body'],
                'audience' => $audience,
            ]);

            $now  = now();
            $rows = [];

            foreach ($userIds as $userId) {
                $rows[] = [
                    'admin_message_id' => $message->id,
                    'user_id'          => $userId,
                    'instructor_id'    => null,
                    'admin_id'         => null,
                    'read_at'          => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            foreach ($adminIds as $aId) {
                $rows[] = [
                    'admin_message_id' => $message->id,
                    'user_id'          => null,
                    'instructor_id'    => null,
                    'admin_id'         => $aId,
                    'read_at'          => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            if (! empty($rows)) {
                foreach (array_chunk($rows, 500) as $chunk) {
                    AdminMessageRecipient::insert($chunk);
                }
            }

            return $message;
        });
    }

    /**
     * Resolve the compose `groups` selection into concrete recipient ids
     * plus the `audience` snapshot used for faithful "All <group>" labels.
     *
     * @param  array<int, array<string, mixed>>  $groups
     * @return array{0: array<int, int>, 1: array<int, int>, 2: array<int, array<string, mixed>>}
     */
    private function resolveGroups(array $groups): array
    {
        $userIds  = [];
        $adminIds = [];
        $audience = [];
        $morph    = (new Admin())->getMorphClass();

        foreach ($groups as $group) {
            $type = $group['type'] ?? null;
            $all  = (bool) ($group['all'] ?? false);
            $ids  = array_map('intval', $group['ids'] ?? []);

            if ($type === 'learner') {
                $resolved = $all
                    ? User::query()->pluck('id')->map(fn ($v) => (int) $v)->all()
                    : User::query()->whereIn('id', $ids)->pluck('id')->map(fn ($v) => (int) $v)->all();

                $userIds = array_merge($userIds, $resolved);

                if (! empty($resolved)) {
                    $audience[] = [
                        'type'    => 'learner',
                        'role_id' => null,
                        'all'     => $all,
                        'count'   => count($resolved),
                    ];
                }
            } elseif ($type === 'role') {
                $roleId = (int) ($group['role_id'] ?? 0);
                if ($roleId <= 0) {
                    continue;
                }

                $roleAdminIds = DB::table('model_has_roles')
                    ->where('role_id', $roleId)
                    ->where('model_type', $morph)
                    ->pluck('model_id')
                    ->map(fn ($v) => (int) $v)
                    ->all();

                $resolved = $all
                    ? $roleAdminIds
                    : array_values(array_intersect($roleAdminIds, $ids));

                $adminIds = array_merge($adminIds, $resolved);

                if (! empty($resolved)) {
                    $audience[] = [
                        'type'    => 'role',
                        'role_id' => $roleId,
                        'all'     => $all,
                        'count'   => count($resolved),
                    ];
                }
            }
        }

        return [
            array_values(array_unique($userIds)),
            array_values(array_unique($adminIds)),
            $audience,
        ];
    }

    /**
     * Mark a message as read for the authenticated entity.
     * Supports learner (User), instructor (Instructor) and admin recipients.
     */
    public function markRead(AdminMessage $message, Model $reader): void
    {
        $column = match (true) {
            $reader instanceof \App\Models\Admin      => 'admin_id',
            $reader instanceof \App\Models\Instructor => 'instructor_id',
            default                                    => 'user_id',
        };

        AdminMessageRecipient::query()
            ->where('admin_message_id', $message->id)
            ->where($column, $reader->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Admin override: mark ALL recipients of a message as read.
     */
    public function markAllRead(AdminMessage $message): int
    {
        return AdminMessageRecipient::query()
            ->where('admin_message_id', $message->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function roleLabel(object $role, string $locale): string
    {
        $en = $role->name_en ?? null;
        $ar = $role->name_ar ?? null;

        $display = $locale === 'ar' ? ($ar ?: $en) : ($en ?: $ar);

        return (string) ($display ?: ucwords(str_replace(['_', '-'], ' ', (string) $role->name)));
    }
}
