<?php

namespace Afterburner\Playbook;

use Afterburner\Playbook\Support\PlaybookPermissions;
use App\Models\User;

class PlaybookSection
{
    /**
     * @param  callable|null  $enabled
     * @param  callable|null  $permission
     */
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly string $path,
        public readonly int $order = 100,
        public readonly mixed $enabled = null,
        public readonly mixed $permission = null,
    ) {}

    public function isEnabled(): bool
    {
        if ($this->enabled === null) {
            return true;
        }

        if (is_callable($this->enabled)) {
            return (bool) ($this->enabled)();
        }

        return (bool) $this->enabled;
    }

    public function isVisibleTo(?object $user): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        if ($this->permission === null) {
            if (! $user instanceof User || ! $user->currentTeam) {
                return false;
            }

            return PlaybookPermissions::canAccessModule($user, $user->currentTeam);
        }

        if (! is_callable($this->permission)) {
            return (bool) $this->permission;
        }

        return (bool) ($this->permission)($user);
    }
}
