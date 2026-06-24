<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AdminMessageResource extends JsonResource
{
    /** @var array<int, array{name_en:?string, name_ar:?string, name:string}>|null */
    private static ?array $roleMap = null;

    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $myUnread = $this->my_unread_count;
        $isRead   = $myUnread === null ? null : ((int) $myUnread === 0);

        $groups = $this->recipientGroups($locale);

        return [
            'id'                => $this->id,
            'subject'           => $this->subject,
            'body'              => $this->body,
            'preview'           => $this->body ? mb_substr(strip_tags($this->body), 0, 140) : '',
            'created_at'        => $this->created_at?->toDateTimeString(),

            // Sender (the back-office admin who composed the message).
            'sender'            => $this->whenLoaded('admin', fn () => $this->admin ? [
                'id'   => $this->admin->id,
                'name' => $this->admin->name,
            ] : null),

            // Counts.
            'read_count'        => $this->read_count ?? null,
            'recipients_count'  => $this->total_recipients ?? $this->recipients_count ?? null,
            'total_recipients'  => $this->total_recipients ?? null,
            'is_read'           => $isRead,

            // Audience-driven recipient grouping (Sent view + detail popup).
            'recipient_groups'  => $groups,
            'recipients_summary'=> $this->summaryFor($groups),

            // Flat recipient name list (detail popup, when recipients loaded).
            'recipients'        => $this->whenLoaded('recipients', fn () =>
                $this->recipients->map(function ($recipient) use ($locale) {
                    $person = $recipient->admin_id !== null
                        ? $recipient->admin
                        : ($recipient->instructor_id !== null ? $recipient->instructor : $recipient->user);

                    $name = $person
                        ? (method_exists($person, 'getLocalizedName')
                            ? $person->getLocalizedName()
                            : (method_exists($person, 'getTranslation')
                                ? $person->getTranslation('name', $locale)
                                : $person->name))
                        : null;

                    return [
                        'id'      => $person?->id,
                        'name'    => $name,
                        'kind'    => $recipient->admin_id !== null ? 'admin'
                            : ($recipient->instructor_id !== null ? 'instructor' : 'learner'),
                        'read_at' => $recipient->read_at?->toDateTimeString(),
                    ];
                })->filter(fn ($r) => $r['name'] !== null)->values()
            ),
        ];
    }

    /**
     * Build display groups from the stored `audience` snapshot.
     *
     * @return array<int, array{type:string, role_id:?int, label:string, all:bool, count:int}>
     */
    private function recipientGroups(string $locale): array
    {
        $audience = is_array($this->audience) ? $this->audience : [];
        $out      = [];

        foreach ($audience as $group) {
            $type = $group['type'] ?? null;

            if ($type === 'learner') {
                $out[] = [
                    'type'    => 'learner',
                    'role_id' => null,
                    'label'   => __('messages.inbox.learners'),
                    'all'     => (bool) ($group['all'] ?? false),
                    'count'   => (int) ($group['count'] ?? 0),
                ];
            } elseif ($type === 'role') {
                $roleId = (int) ($group['role_id'] ?? 0);
                $out[]  = [
                    'type'    => 'role',
                    'role_id' => $roleId,
                    'label'   => $this->roleLabel($roleId, $locale),
                    'all'     => (bool) ($group['all'] ?? false),
                    'count'   => (int) ($group['count'] ?? 0),
                ];
            }
        }

        return $out;
    }

    /**
     * One-line recipients summary for the Sent list item, e.g.
     * "All Learners", "All Instructors", or "All Learners, 3 Admin".
     *
     * @param  array<int, array{type:string, role_id:?int, label:string, all:bool, count:int}>  $groups
     */
    private function summaryFor(array $groups): ?string
    {
        if (empty($groups)) {
            return null;
        }

        $parts = array_map(function ($g) {
            return $g['all']
                ? __('messages.inbox.all_of', ['group' => $g['label']])
                : $g['count'] . ' ' . $g['label'];
        }, $groups);

        return implode(app()->getLocale() === 'ar' ? '، ' : ', ', $parts);
    }

    private function roleLabel(int $roleId, string $locale): string
    {
        if (self::$roleMap === null) {
            self::$roleMap = DB::table('roles')
                ->where('guard_name', 'admin')
                ->get(['id', 'name', 'name_en', 'name_ar'])
                ->keyBy('id')
                ->map(fn ($r) => [
                    'name'    => (string) $r->name,
                    'name_en' => $r->name_en,
                    'name_ar' => $r->name_ar,
                ])
                ->all();
        }

        $row = self::$roleMap[$roleId] ?? null;
        if (! $row) {
            return (string) __('messages.inbox.recipients');
        }

        $display = $locale === 'ar' ? ($row['name_ar'] ?: $row['name_en']) : ($row['name_en'] ?: $row['name_ar']);

        return (string) ($display ?: ucwords(str_replace(['_', '-'], ' ', $row['name'])));
    }
}
