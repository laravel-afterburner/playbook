<?php

namespace Afterburner\Playbook\Support;

use Afterburner\Support\EntityLabel;

class PlaybookPlaceholders
{
    public static function replace(string $content): string
    {
        $replacements = [
            '/playbook/' => '/'.HelpSupportRoute::PREFIX.'/',
            '{{ entity_label }}' => EntityLabel::singular(),
            '{{ entity_label_title }}' => EntityLabel::singularTitle(),
            '{{ entity_label_plural }}' => EntityLabel::plural(),
            '{{ entity_label_plural_title }}' => EntityLabel::pluralTitle(),
            '{{ app_name }}' => (string) config('afterburner.app_name', config('app.name')),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
