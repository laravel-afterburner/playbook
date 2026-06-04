<?php

namespace Afterburner\Playbook\Support;

final class PlaybookPermissionDefinitions
{
    public const VIEW_PLAYBOOK = 'view_playbook';

    public const VIEW_FAQS = 'view_playbook_faqs';

    public const MANAGE_FAQS = 'manage_playbook_faqs';

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return [
            self::VIEW_PLAYBOOK,
            self::VIEW_FAQS,
            self::MANAGE_FAQS,
        ];
    }

    /**
     * @return array<int, array{name: string, slug: string, description: string}>
     */
    public static function all(): array
    {
        if (class_exists(\App\Support\PermissionCatalog::class)) {
            return collect(\App\Support\PermissionCatalog::definitions())
                ->filter(fn (array $permission) => in_array($permission['slug'], self::slugs(), true))
                ->values()
                ->all();
        }

        return [
            [
                'name' => 'View Help & Playbook',
                'slug' => self::VIEW_PLAYBOOK,
                'description' => 'Access in-app help documentation',
            ],
            [
                'name' => 'View Playbook FAQs',
                'slug' => self::VIEW_FAQS,
                'description' => 'View published help FAQs',
            ],
            [
                'name' => 'Manage Playbook FAQs',
                'slug' => self::MANAGE_FAQS,
                'description' => 'Create, edit, delete, reorder, and publish help FAQs',
            ],
        ];
    }
}
