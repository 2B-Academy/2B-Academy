<?php

use App\Http\Controllers\apis\ConversationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Conversations — /api/v1/conversations
|--------------------------------------------------------------------------
| The unified two-way messaging store shared by the learner website widget
| and the admin/instructor dashboard inbox. Any authenticated principal
| (User / Admin / Instructor) — the service resolves identities across
| same-email rows, so an instructor signed in as a User still sees their
| instructor threads.
*/
Route::middleware('auth.user')->prefix('conversations')->group(function () {
    Route::get('/',             [ConversationController::class, 'index']);
    Route::get('unread-count',  [ConversationController::class, 'unreadCount']);
    Route::get('recipients',    [ConversationController::class, 'recipients']);
    Route::post('/',            [ConversationController::class, 'store']);
    Route::post('bulk',         [ConversationController::class, 'bulk']);
    Route::get('{conversation}', [ConversationController::class, 'show'])
        ->whereNumber('conversation');
    Route::post('{conversation}/reply', [ConversationController::class, 'reply'])
        ->whereNumber('conversation');
});
