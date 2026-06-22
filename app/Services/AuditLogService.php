<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogService
{
    public function list(int $perPage = 20, ?string $search = null, ?string $action = null): LengthAwarePaginator
    {
        return AuditLog::query()
            ->when($search, fn ($q) => $q->where('user_name', 'like', "%{$search}%")
                                          ->orWhere('description', 'like', "%{$search}%"))
            ->when($action, fn ($q) => $q->where('action', $action))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public static function log(string $action, ?string $description = null, ?string $modelType = null, ?int $modelId = null): void
    {
        $user = auth()->user();
        AuditLog::create([
            'user_type'   => $user ? (get_class($user) === \App\Models\Admin::class ? 'admin' : 'user') : 'system',
            'user_id'     => $user?->id,
            'user_name'   => $user?->name,
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}
