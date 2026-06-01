<?php

namespace Afterburner\Playbook\Support;

use Afterburner\Playbook\Models\PlaybookFaq;

class PlaybookFaqNavigation
{
    public static function isVisible(?object $user): bool
    {
        if (PlaybookAccess::isSystemAdmin($user)) {
            return true;
        }

        return PlaybookFaq::query()->published()->exists();
    }
}
