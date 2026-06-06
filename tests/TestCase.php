<?php

namespace Afterburner\Playbook\Tests;

use Afterburner\Playbook\Providers\PlaybookServiceProvider;
use Tests\Concerns\ConfiguresAfterburnerEntity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use ConfiguresAfterburnerEntity;
    use RefreshDatabase;

    protected function defineRoutes($router): void
    {
        $router->get('/login', fn () => redirect('/'))->name('login');
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'afterburner-playbook.enabled' => true,
            'afterburner.app_name' => 'Test App',
        ]);

        $this->configureAfterburnerEntity('strata', 'strata');
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            PlaybookServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        static::applyAfterburnerEntityConfig($app, 'strata', 'strata');

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function createVerifiedUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ], $attributes));
    }
}
