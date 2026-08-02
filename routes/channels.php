<?php

use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Instructor;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Registered separately from the framework default (see bootstrap/app.php
| ->withBroadcasting()) behind our own bearer-token `auth.user` middleware,
| so `$principal` below is whatever `AuthenticationMiddleware` resolved
| (a User, Instructor, or Admin model) — never the session `web` guard.
|
*/

/** One physical account's own private channel — its inbox list + unread badge. */
Broadcast::channel('identity.{type}.{id}', function ($principal, string $type, int $id) {
    $expected = match ($type) {
        'User'       => User::class,
        'Instructor' => Instructor::class,
        'Admin'      => Admin::class,
        default      => null,
    };

    return $expected !== null
        && $principal::class === $expected
        && (int) $principal->getKey() === $id;
});

/** A conversation thread — anyone (under any of their identities) party to it. */
Broadcast::channel('conversation.{id}', function ($principal, int $id) {
    $conversation = Conversation::find($id);

    return $conversation !== null && app(MessageService::class)->isParticipant($principal, $conversation);
});
