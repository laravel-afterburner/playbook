<?php

namespace Afterburner\Playbook\Tests\Unit;

use Afterburner\Playbook\PlaybookRepository;
use Afterburner\Playbook\Support\Playbook;
use Afterburner\Playbook\Tests\TestCase;

class PlaybookRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Playbook::clear();
        app(PlaybookRepository::class)->flush();
    }

    protected function tearDown(): void
    {
        Playbook::clear();
        app(PlaybookRepository::class)->flush();

        parent::tearDown();
    }

    public function test_it_discovers_platform_pages(): void
    {
        Playbook::register([
            'key' => 'platform',
            'label' => 'Platform',
            'order' => 0,
            'path' => dirname(__DIR__, 2).'/playbook/platform',
        ]);

        $repository = app(PlaybookRepository::class);
        $pages = $repository->pagesForSection('platform');

        $this->assertTrue($pages->contains(fn ($page) => $page->slug === 'welcome'));
        $this->assertTrue($pages->contains(fn ($page) => $page->slug === 'navigation'));
    }

    public function test_it_finds_default_page(): void
    {
        Playbook::register([
            'key' => 'platform',
            'label' => 'Platform',
            'order' => 0,
            'path' => dirname(__DIR__, 2).'/playbook/platform',
        ]);

        $page = app(PlaybookRepository::class)->defaultPage();

        $this->assertNotNull($page);
        $this->assertSame('welcome', $page->slug);
    }

    public function test_it_interpolates_placeholders_when_rendering(): void
    {
        Playbook::register([
            'key' => 'platform',
            'label' => 'Platform',
            'order' => 0,
            'path' => dirname(__DIR__, 2).'/playbook/platform',
        ]);

        $repository = app(PlaybookRepository::class);
        $page = $repository->findPage('platform', 'welcome');
        $rendered = app(\Afterburner\Playbook\PlaybookRenderer::class)->renderPage($page);

        $this->assertStringContainsString('Test App', $rendered['html']);
        $this->assertStringContainsString('strata', $rendered['html']);
    }
}
