<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Role;
use App\Models\User;

class NotificationService
{
    /**
     * Notify a specific user.
     */
    public static function notifyUser(
        User|int $user,
        string $type,
        string $title,
        string $message,
        string $module = 'clinical',
        ?string $targetUrl = null,
        string $priority = 'normal'
    ): ?Notification {
        $userId = $user instanceof User ? $user->id : $user;

        if (!$userId) {
            return null;
        }

        return Notification::create([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'module'     => $module,
            'target_url' => $targetUrl,
            'priority'   => $priority,
            'is_read'    => false,
        ]);
    }

    /**
     * Notify all active users assigned to a specific role.
     */
    public static function notifyRole(
        string $roleSlug,
        string $type,
        string $title,
        string $message,
        string $module = 'clinical',
        ?string $targetUrl = null,
        string $priority = 'normal'
    ): void {
        $role = Role::where('slug', $roleSlug)->first();
        if (!$role) {
            return;
        }

        $users = $role->users()->where('is_active', true)->get();

        foreach ($users as $user) {
            static::notifyUser($user, $type, $title, $message, $module, $targetUrl, $priority);
        }
    }
}
