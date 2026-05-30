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
    | When true, a Help & Support link is added to the entity dropdown menu.
    |
    */

    'navigation_enabled' => env('AFTERBURNER_PLAYBOOK_NAVIGATION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Default Section
    |--------------------------------------------------------------------------
    |
    | The section and page slug used when visiting /help.
    |
    */

    'default_section' => env('AFTERBURNER_PLAYBOOK_DEFAULT_SECTION', 'platform'),

    'default_page' => env('AFTERBURNER_PLAYBOOK_DEFAULT_PAGE', 'welcome'),

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    'search' => [
        'min_query_length' => (int) env('AFTERBURNER_PLAYBOOK_SEARCH_MIN_QUERY_LENGTH', 2),
        'max_results' => (int) env('AFTERBURNER_PLAYBOOK_SEARCH_MAX_RESULTS', 20),
        'cache_ttl' => env('AFTERBURNER_PLAYBOOK_SEARCH_CACHE_TTL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Support
    |--------------------------------------------------------------------------
    */

    'contact_support' => [
        'subject_max_length' => (int) env('AFTERBURNER_PLAYBOOK_SUPPORT_SUBJECT_MAX_LENGTH', 255),
        'message_max_length' => (int) env('AFTERBURNER_PLAYBOOK_SUPPORT_MESSAGE_MAX_LENGTH', 5000),
    ],

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
