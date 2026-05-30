<?php

namespace Afterburner\Playbook\Support;

final class HelpSupportRoute
{
    public const PREFIX = 'help';

    public static function uri(string $section = '', string $page = ''): string
    {
        if ($page !== '') {
            return '/'.self::PREFIX.'/'.$section.'/'.$page;
        }

        if ($section !== '') {
            return '/'.self::PREFIX.'/'.$section;
        }

        return '/'.self::PREFIX;
    }
}
