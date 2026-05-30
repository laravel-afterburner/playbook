<?php

namespace Afterburner\Playbook\Support;

use Illuminate\Support\Str;

class PlaybookPlaceholders
{
    public static function replace(string $content): string
    {
        $entityLabel = (string) config('afterburner.entity_label', 'entity');

        $replacements = [
            '/playbook/' => '/'.HelpSupportRoute::PREFIX.'/',
            '{{ entity_label }}' => $entityLabel,
            '{{ entity_label_title }}' => Str::title($entityLabel),
            '{{ entity_label_plural }}' => Str::plural($entityLabel),
            '{{ entity_label_plural_title }}' => Str::title(Str::plural($entityLabel)),
            '{{ app_name }}' => (string) config('afterburner.app_name', config('app.name')),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
