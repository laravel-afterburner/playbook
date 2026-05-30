<?php

namespace Afterburner\Playbook\Tests\Unit;

use Afterburner\Playbook\Livewire\PlaybookSearch;
use Afterburner\Playbook\PlaybookRepository;
use Afterburner\Playbook\Support\HelpSupportRoute;
use Afterburner\Playbook\Support\Playbook;
use Afterburner\Playbook\Tests\TestCase;
use Livewire\Livewire;

class PlaybookUiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Playbook::clear();
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
        app(PlaybookRepository::class)->flush();

        parent::tearDown();
    }

    public function test_routes_use_help_url_prefix(): void
    {
        $this->assertSame(HelpSupportRoute::uri(), route('playbook.index', [], false));
        $this->assertSame(
            HelpSupportRoute::uri('platform', 'welcome'),
            route('playbook.show', ['section' => 'platform', 'page' => 'welcome'], false)
        );
    }

    public function test_search_component_uses_help_and_support_labels(): void
    {
        Livewire::test(PlaybookSearch::class)
            ->assertSee('Search Help & Support')
            ->assertDontSee('Search playbook');
    }
}
