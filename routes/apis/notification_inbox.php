<?php

use App\Http\Controllers\apis\NotificationInboxController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| "My Notifications" Routes — /api/v1/notifications/mine
|--------------------------------------------------------------------------
| Event-driven notification inbox (pending grading, rating drops,
| assignment completion, course assignment, cohort creation). Separate
| from routes/apis/notifications.php, which is the admin-composed
| broadcast/announcement tool.
*/

Route::middleware(['auth.user'])->prefix('notifications/mine')->group(function () {
    Route::get('/',            [NotificationInboxController::class, 'index']);
    Route::get('unread-count', [NotificationInboxController::class, 'unreadCount']);
    Route::post('read-all',    [NotificationInboxController::class, 'markAllRead']);
    Route::post('{id}/read',   [NotificationInboxController::class, 'markRead']);
});

// Admin oversight — instructors have no authentication of their own yet,
// so this lets Admins inspect what an instructor received.
Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('instructors/{instructor}/notifications', [NotificationInboxController::class, 'forInstructor']);
});
