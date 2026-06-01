<?php

namespace Afterburner\Playbook\Providers;

use Afterburner\Playbook\Console\Commands\InstallCommand;
use Afterburner\Playbook\Console\Commands\ValidatePlaybookCommand;
use Afterburner\Playbook\Livewire\PlaybookContactSupport;
use Afterburner\Playbook\Livewire\PlaybookFaqSection;
use Afterburner\Playbook\Livewire\PlaybookSearch;
use Afterburner\Playbook\PlaybookRenderer;
use Afterburner\Playbook\PlaybookRepository;
use Afterburner\Playbook\PlaybookSearchService;
use Afterburner\Playbook\Support\Playbook;
use Afterburner\Playbook\Support\PlaybookFaqNavigation;
use Afterburner\Playbook\Support\UiDisplayName;
use App\Models\Team;
use App\Support\TeamNavigation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        $this->app->singleton(PlaybookSearchService::class);
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

        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'afterburner-playbook-migrations');

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'afterburner-playbook');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        View::composer('afterburner-playbook::*', function ($view): void {
            $view->with('helpSupportName', UiDisplayName::LABEL);
            $view->with('showFaqNav', PlaybookFaqNavigation::isVisible(auth()->user()));
        });

        Livewire::component('playbook-search', PlaybookSearch::class);
        Livewire::component('playbook-contact-support', PlaybookContactSupport::class);
        Livewire::component('playbook-faq-section', PlaybookFaqSection::class);

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
            'label' => UiDisplayName::LABEL,
            'route' => 'playbook.index',
            'route_params' => fn () => [],
            'order' => 20,
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
