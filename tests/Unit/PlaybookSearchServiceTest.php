<?php

namespace Afterburner\Playbook\Tests\Unit;

use Afterburner\Playbook\Livewire\PlaybookSearch;
use Afterburner\Playbook\PlaybookRepository;
use Afterburner\Playbook\PlaybookSearchService;
use Afterburner\Playbook\Support\Playbook;
use Afterburner\Playbook\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

class PlaybookSearchServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Playbook::clear();
        Cache::flush();
        app(PlaybookSearchService::class)->flush();
        app(PlaybookRepository::class)->flush();

        Playbook::register([
            'key' => 'platform',
            'label' => 'Platform',
            'order' => 0,
            'path' => dirname(__DIR__, 2).'/playbook/platform',
        ]);
    }

    protected function tearDown(): void
    {
        Playbook::clear();
        Cache::flush();
        app(PlaybookSearchService::class)->flush();
        app(PlaybookRepository::class)->flush();

        parent::tearDown();
    }

    public function test_it_finds_pages_by_title(): void
    {
        $results = app(PlaybookSearchService::class)->search('welcome', null);

        $this->assertTrue($results->contains(fn (array $result) => $result['slug'] === 'welcome'));
    }

    public function test_it_finds_pages_by_body_content(): void
    {
        $results = app(PlaybookSearchService::class)->search('impersonation', null);

        $this->assertTrue($results->contains(fn (array $result) => $result['slug'] === 'welcome'));
    }

    public function test_it_requires_minimum_query_length(): void
    {
        config(['afterburner-playbook.search.min_query_length' => 2]);

        $results = app(PlaybookSearchService::class)->search('w', null);

        $this->assertTrue($results->isEmpty());
    }

    public function test_title_matches_rank_above_body_matches(): void
    {
        $results = app(PlaybookSearchService::class)->search('welcome', null);

        $this->assertSame('welcome', $results->first()['slug']);
    }

    public function test_system_admin_pages_are_hidden_from_regular_users(): void
    {
        $user = $this->createVerifiedUser(['is_system_admin' => false]);
        $results = app(PlaybookSearchService::class)->search('audit', $user);

        $this->assertFalse($results->contains(fn (array $result) => $result['slug'] === 'audit-trail'));
    }

    public function test_system_admin_pages_are_visible_to_system_admins(): void
    {
        $user = $this->createVerifiedUser(['is_system_admin' => true]);
        $results = app(PlaybookSearchService::class)->search('audit', $user);

        $this->assertTrue($results->contains(fn (array $result) => $result['slug'] === 'audit-trail'));
    }

    public function test_repository_flush_clears_search_cache(): void
    {
        app(PlaybookSearchService::class)->search('welcome', null);

        $this->assertTrue(Cache::has(PlaybookSearchService::CACHE_KEY));

        app(PlaybookRepository::class)->flush();

        $this->assertFalse(Cache::has(PlaybookSearchService::CACHE_KEY));
    }

    public function test_livewire_component_returns_results_for_valid_query(): void
    {
        $user = $this->createVerifiedUser();

        Livewire::actingAs($user)
            ->test(PlaybookSearch::class)
            ->set('query', 'welcome')
            ->assertSee('Welcome');
    }
}
