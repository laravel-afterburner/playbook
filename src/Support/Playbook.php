<?php

namespace Afterburner\Playbook\Support;

use Afterburner\Playbook\PlaybookSection;

class Playbook
{
    /** @var array<int, array<string, mixed>> */
    protected static array $sections = [];

    /**
     * Register a playbook documentation section.
     *
     * @param  array<string, mixed>  $section
     */
    public static function register(array $section): void
    {
        self::$sections[] = array_merge([
            'order' => 100,
            'enabled' => null,
            'permission' => null,
        ], $section);
    }

    /**
     * @return list<PlaybookSection>
     */
    public static function sections(): array
    {
        return collect(self::$sections)
            ->map(function (array $section) {
                return new PlaybookSection(
                    key: (string) $section['key'],
                    label: (string) $section['label'],
                    path: (string) $section['path'],
                    order: (int) ($section['order'] ?? 100),
                    enabled: $section['enabled'] ?? null,
                    permission: $section['permission'] ?? null,
                );
            })
            ->filter(fn (PlaybookSection $section) => $section->isEnabled())
            ->sortBy('order')
            ->values()
            ->all();
    }

    /**
     * @return list<PlaybookSection>
     */
    public static function visibleSections(?object $user): array
    {
        return array_values(array_filter(
            self::sections(),
            fn (PlaybookSection $section) => $section->isVisibleTo($user)
        ));
    }

    public static function clear(): void
    {
        self::$sections = [];
    }
}
