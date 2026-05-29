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

Visit `/playbook` when signed in. A **Playbook** link is added to the entity dropdown menu by default.

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

// Entity menu link (after Members):
TeamNavigation::register([
    'label' => 'Playbook',
    'route' => 'playbook.index',
    'placement' => 'after-members',
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
feature: team-announcements
system_admin: false
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
| `feature` | Hide unless `App\Support\Features` flag is enabled |
| `system_admin` | Restrict to system administrators (`true` by default for pages under a `system-admin/` folder) |

### Placeholders

Use these in markdown body copy:

- `{{ entity_label }}` — e.g. `strata`, `team`
- `{{ entity_label_title }}` — e.g. `Strata`
- `{{ entity_label_plural }}` — e.g. `stratas`, `teams`
- `{{ app_name }}` — configured application name

### Internal links

```markdown
See [Entity overview](/playbook/platform/entity-overview).
```

Validate links:

```bash
php artisan playbook:validate
```

## Configuration

Publish config with `php artisan vendor:publish --tag=afterburner-playbook-config`.

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
