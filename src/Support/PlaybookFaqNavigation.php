<?php

namespace Afterburner\Playbook\Support;

use App\Models\User;

class PlaybookFaqNavigation
{
    public static function isVisible(?object $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return PlaybookPermissions::canViewFaqs($user, $user->currentTeam);
    }
}
