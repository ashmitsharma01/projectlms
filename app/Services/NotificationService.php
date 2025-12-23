<?php

namespace App\Services;

use App\Notifications\InAppNotification;

class NotificationService
{
    public static function send($user, array $data)
    {
        $user->notify(new InAppNotification($data));
    }
}
