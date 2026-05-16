<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Private per-user channel — only the authenticated user can subscribe to their own channel.
Broadcast::channel('users.{userId}', function (User $user, string $userId): bool {
    return $user->id === $userId;
});
