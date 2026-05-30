<?php

namespace Afterburner\Playbook\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SystemAdminRecipients
{
    /**
     * @return Collection<int, Model>
     */
    public static function all(): Collection
    {
        $model = config('auth.providers.users.model');

        if (! is_string($model) || ! class_exists($model)) {
            return collect();
        }

        return $model::query()
            ->where('is_system_admin', true)
            ->get()
            ->filter(fn ($user) => PlaybookAccess::isSystemAdmin($user))
            ->values();
    }
}
