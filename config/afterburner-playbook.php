<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Playbook Enabled
    |--------------------------------------------------------------------------
    */

    'enabled' => env('AFTERBURNER_PLAYBOOK_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | When true, a Playbook link is added to the entity dropdown menu.
    |
    */

    'navigation_enabled' => env('AFTERBURNER_PLAYBOOK_NAVIGATION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Default Section
    |--------------------------------------------------------------------------
    |
    | The section and page slug used when visiting /playbook.
    |
    */

    'default_section' => env('AFTERBURNER_PLAYBOOK_DEFAULT_SECTION', 'platform'),

    'default_page' => env('AFTERBURNER_PLAYBOOK_DEFAULT_PAGE', 'welcome'),

    /*
    |--------------------------------------------------------------------------
    | Audit Skip Routes
    |--------------------------------------------------------------------------
    */

    'audit' => [
        'skip_routes' => [
            'playbook.index',
            'playbook.section',
            'playbook.show',
        ],
    ],

];
