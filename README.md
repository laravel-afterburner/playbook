# Afterburner Playbook

In-app documentation for Afterburner applications. The playbook ships platform guides and aggregates documentation from optional packages through a registry — similar to how navigation items are registered.

## Installation

```bash
composer require laravel-afterburner/playbook
php artisan afterburner:playbook:install
```

Add the path repository when developing locally:

```json
"repositories": {
    "afterburner-playbook": {
        "type": "path",
        "url": "../afterburner-playbook"
    }
}
```

## Usage

Visit `/help` when signed in. A **Help & Support** link is registered via `TeamNavigation` (default placement `system-support` in strata apps, or `default` / order 20 in other hosts).

### FAQ

Visit `/help/faq` for frequently asked questions. FAQ access is separate from playbook documentation:

| Permission slug | Purpose |
| --- | --- |
| `view_playbook` | Read in-app help documentation |
| `view_playbook_faqs` | View published FAQs |
| `manage_playbook_faqs` | Create, edit, reorder, publish, and delete FAQs (implies view) |

In Strata, assign these under **Help** in Role Manager. `manage_playbook_faqs` bundles `view_playbook_faqs`. Users can hold FAQ permissions without `view_playbook`.

When team RBAC is unavailable (standalone package tests), published FAQs remain visible to authenticated users. System administrators always have full FAQ access.

After installing or upgrading, run migrations so the `playbook_faqs` table exists:

```bash
php artisan migrate
```

## Registering a section from a package

```php
use Afterburner\Playbook\Support\Playbook;

// In your ServiceProvider::boot()
if (class_exists(Playbook::class)) {
    Playbook::register([
        'key' => 'meetings',
        'label' => 'Meetings',
        'order' => 20,
        'path' => __DIR__.'/../../playbook',
        'enabled' => fn () => config('afterburner-meetings.enabled', true),
        'permission' => fn ($user) => $user?->can('viewAny', Meeting::class) ?? false,
    ]);
}

// Entity menu link (order > Subscriptions' 15, default placement):
TeamNavigation::register([
    'label' => 'Help & Support',
    'route' => 'playbook.index',
    'order' => 20,
    ...
]);
```

Ship markdown files under `playbook/` in your package:

```
playbook/
  scheduling-an-agm.md
  meeting-notices.md
```

### Page front matter

```markdown
---
title: Scheduling an AGM
slug: scheduling-an-agm
order: 10
group: scheduling
group_order: 100
---

## Before you begin
...
```

Supported front matter keys:

| Key | Description |
|-----|-------------|
| `title` | Page title (required) |
| `slug` | URL slug (defaults to filename) |
| `order` | Sort order within group |
| `group` | Sidebar group label (defaults to folder name) |
| `group_order` | Sort order for sidebar groups (defaults to `0` for `getting-started/` folders, otherwise `100`) |
| `feature` | Hide unless `App\Support\Features` flag is enabled (typically used for optional platform security pages) |
| `system_admin` | Restrict to system administrators (`true` by default for pages under a `system-admin/` folder) |

### Placeholders

Use these in markdown body copy:

- `{{ entity_label }}` — e.g. `strata`, `team`
- `{{ entity_label_title }}` — e.g. `Strata`
- `{{ entity_label_plural }}` — e.g. `stratas`, `teams`
- `{{ app_name }}` — configured application name

### Internal links

```markdown
See [Entity overview](/help/platform/entity-overview).
```

Validate links:

```bash
php artisan playbook:validate
```

## Configuration

Publish config with `php artisan vendor:publish --tag=afterburner-playbook-config`.

If you previously published views (`afterburner-playbook-assets`), republish or delete `resources/views/vendor/afterburner-playbook` so UI updates apply — published views override the package.

| Variable | Default | Description |
|----------|---------|-------------|
| `AFTERBURNER_PLAYBOOK_ENABLED` | `true` | Master switch |
| `AFTERBURNER_PLAYBOOK_NAVIGATION_ENABLED` | `true` | Show link in entity menu |
| `AFTERBURNER_PLAYBOOK_DEFAULT_SECTION` | `platform` | Landing section |
| `AFTERBURNER_PLAYBOOK_DEFAULT_PAGE` | `welcome` | Landing page slug |

## Testing

```bash
composer test
```

## License

MIT
