<?php

namespace Afterburner\Playbook\Tests\Unit;

use Afterburner\Playbook\PlaybookRepository;
use Afterburner\Playbook\Support\Playbook;
use Afterburner\Playbook\Tests\TestCase;

class PlaybookSystemAdminVisibilityTest extends TestCase
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

    public function test_system_admin_pages_are_hidden_from_regular_users(): void
    {
        $user = $this->createVerifiedUser(['is_system_admin' => false]);
        $repository = app(PlaybookRepository::class);

        $this->assertNull($repository->findPage('platform', 'audit-trail', $user));
        $this->assertNull($repository->findPage('platform', 'impersonation', $user));

        $slugs = $repository->pagesForSection('platform', $user)
            ->pluck('slug')
            ->all();

        $this->assertNotContains('audit-trail', $slugs);
        $this->assertNotContains('impersonation', $slugs);
    }

    public function test_system_admin_pages_are_visible_to_system_admins(): void
    {
        $user = $this->createVerifiedUser(['is_system_admin' => true]);
        $repository = app(PlaybookRepository::class);

        $this->assertNotNull($repository->findPage('platform', 'audit-trail', $user));
        $this->assertNotNull($repository->findPage('platform', 'impersonation', $user));
    }
}
