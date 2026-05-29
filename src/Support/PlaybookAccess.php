<?php

namespace Afterburner\Playbook\Support;

class PlaybookAccess
{
    public static function isSystemAdmin(?object $user): bool
    {
        return $user !== null
            && method_exists($user, 'isSystemAdmin')
            && $user->isSystemAdmin();
    }
}
