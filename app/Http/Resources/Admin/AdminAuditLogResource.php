<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for a single audit log row in the 2026 admin Audit Log table.
 *
 * Receives a decorated stdClass row from AdminAuditLogService — the
 * service computes `effective_role`, `entity_token`, and `action_token`
 * so the template can stay declarative.
 */
class AdminAuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = $this->resource;

        $actor = trim((string) ($row->user_name ?? ''));
        $role  = (string) ($row->effective_role ?? 'learner');

        return [
            'id'             => (int) ($row->id ?? 0),
            'actor_name'     => $actor !== '' ? $actor : '—',
            'actor_id'       => $row->user_id !== null ? (int) $row->user_id : null,
            'actor_role'     => $role,
            'actor_role_label' => $this->labelForRole($role),
            'avatar_initial' => $this->initial($actor !== '' ? $actor : 'S'),
            'entity_token'   => (string) ($row->entity_token ?? ''),
            'action_token'   => (string) ($row->action_token ?? ''),
            'action_raw'     => (string) ($row->action ?? ''),
            'model_type'     => $row->model_type !== null ? (string) $row->model_type : null,
            'model_id'       => $row->model_id   !== null ? (int)    $row->model_id   : null,
            'entity'         => (string) ($row->description ?? ''),
            'ip_address'     => (string) ($row->ip_address ?? ''),
            'created_at'     => $row->created_at ? (string) $row->created_at : null,
        ];
    }

    private function initial(string $name): string
    {
        $first = mb_substr(trim($name), 0, 1);
        return $first === '' ? 'S' : mb_strtoupper($first);
    }

    private function labelForRole(string $role): string
    {
        return match (strtolower($role)) {
            'admin'      => 'Admin',
            'instructor' => 'Instructor',
            'system'     => 'System',
            default      => 'Learner',
        };
    }
}
