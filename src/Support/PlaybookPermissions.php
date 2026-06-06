<?php

namespace Afterburner\Playbook\Support;

use App\Models\Team;
use App\Models\User;
use App\Support\TeamPermissionGate;

/**
 * Help & playbook module access (documentation vs FAQs).
 */
final class PlaybookPermissions
{
    public const SECTION_HELP = 'help';

    public const SECTION_FAQ = 'faq';

    /**
     * @return array<string, string>
     */
    public static function sectionPermissionMap(): array
    {
        return [
            self::SECTION_HELP => PlaybookPermissionDefinitions::VIEW_PLAYBOOK,
            self::SECTION_FAQ => PlaybookPermissionDefinitions::VIEW_FAQS,
        ];
    }

    /**
     * @return list<string>
     */
    public static function playbookModuleAccessSlugs(): array
    {
        return [PlaybookPermissionDefinitions::VIEW_PLAYBOOK];
    }

    /**
     * @return list<string>
     */
    public static function faqModuleAccessSlugs(): array
    {
        return [
            PlaybookPermissionDefinitions::VIEW_FAQS,
            PlaybookPermissionDefinitions::MANAGE_FAQS,
        ];
    }

    public static function canAccessModule(?User $user, ?Team $team): bool
    {
        return self::canViewPlaybook($user, $team);
    }

    public static function canViewPlaybook(?User $user, ?Team $team): bool
    {
        if ($user === null || $team === null) {
            return false;
        }

        if (! self::belongsToCurrentTeam($user, $team)) {
            return false;
        }

        return TeamPermissionGate::allows($user, $team->id, PlaybookPermissionDefinitions::VIEW_PLAYBOOK);
    }

    public static function canViewFaqs(?User $user, ?Team $team): bool
    {
        if ($user === null) {
            return false;
        }

        if (PlaybookAccess::isSystemAdmin($user)) {
            return true;
        }

        if (! self::teamPermissionsAvailable()) {
            return self::legacyPublishedFaqsVisible();
        }

        if ($team === null || ! self::belongsToCurrentTeam($user, $team)) {
            return false;
        }

        return TeamPermissionGate::allowsAny($user, $team->id, self::faqModuleAccessSlugs());
    }

    public static function canManageFaqs(?User $user, ?Team $team): bool
    {
        if ($user === null) {
            return false;
        }

        if (PlaybookAccess::isSystemAdmin($user)) {
            return true;
        }

        if (! self::teamPermissionsAvailable()) {
            return false;
        }

        if ($team === null || ! self::belongsToCurrentTeam($user, $team)) {
            return false;
        }

        return TeamPermissionGate::allows($user, $team->id, PlaybookPermissionDefinitions::MANAGE_FAQS);
    }

    public static function canViewSection(?User $user, ?Team $team, string $section): bool
    {
        return match ($section) {
            self::SECTION_FAQ => self::canViewFaqs($user, $team),
            default => self::canViewPlaybook($user, $team),
        };
    }

    protected static function belongsToCurrentTeam(User $user, Team $team): bool
    {
        if (! method_exists($user, 'belongsToTeam')) {
            return true;
        }

        return $user->belongsToTeam($team) && $user->currentTeam?->id === $team->id;
    }

    protected static function teamPermissionsAvailable(): bool
    {
        return class_exists(\App\Support\PermissionCatalog::class);
    }

    protected static function legacyPublishedFaqsVisible(): bool
    {
        return \Afterburner\Playbook\Models\PlaybookFaq::query()->published()->exists();
    }
}
