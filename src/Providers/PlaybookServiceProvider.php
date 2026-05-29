<?php

namespace Afterburner\Playbook\Providers;

use Afterburner\Playbook\Console\Commands\InstallCommand;
use Afterburner\Playbook\Console\Commands\ValidatePlaybookCommand;
use Afterburner\Playbook\PlaybookRepository;
use Afterburner\Playbook\PlaybookRenderer;
use Afterburner\Playbook\Support\Playbook;
use App\Models\Team;
use App\Support\TeamNavigation;
use Illuminate\Support\ServiceProvider;

class PlaybookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! class_exists(Team::class)) {
            return;
        }

        $this->mergeConfigFrom(
            __DIR__.'/../../config/afterburner-playbook.php',
            'afterburner-playbook'
        );

        $this->app->singleton(PlaybookRepository::class);
        $this->app->singleton(PlaybookRenderer::class);
    }

    public function boot(): void
    {
        if (! class_exists(Team::class) || ! config('afterburner-playbook.enabled', true)) {
            return;
        }

        $this->publishes([
            __DIR__.'/../../config/afterburner-playbook.php' => config_path('afterburner-playbook.php'),
        ], 'afterburner-playbook-config');

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/afterburner-playbook'),
        ], 'afterburner-playbook-assets');

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'afterburner-playbook');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        $this->registerPlatformSection();
        $this->registerEntityNavigation();
        $this->registerAuditSkipRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                ValidatePlaybookCommand::class,
            ]);
        }
    }

    protected function registerPlatformSection(): void
    {
        Playbook::register([
            'key' => 'platform',
            'label' => 'Platform',
            'order' => 0,
            'path' => __DIR__.'/../../playbook/platform',
        ]);
    }

    protected function registerEntityNavigation(): void
    {
        if (! config('afterburner-playbook.navigation_enabled', true)) {
            return;
        }

        if (! class_exists(TeamNavigation::class)) {
            return;
        }

        TeamNavigation::register([
            'label' => 'Playbook',
            'route' => 'playbook.index',
            'route_params' => fn () => [],
            'placement' => 'after-members',
            'order' => 10,
            'permission' => fn ($user) => $user?->currentTeam !== null,
            'active' => fn () => request()->routeIs('playbook.*'),
        ]);
    }

    protected function registerAuditSkipRoutes(): void
    {
        if (! config()->has('audit.skip_routes')) {
            return;
        }

        $skipRoutes = config('afterburner-playbook.audit.skip_routes', []);

        config([
            'audit.skip_routes' => array_values(array_unique(array_merge(
                config('audit.skip_routes', []),
                $skipRoutes
            ))),
        ]);
    }
}
