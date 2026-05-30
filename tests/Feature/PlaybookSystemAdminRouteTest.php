<?php

namespace Afterburner\Playbook\Tests\Feature;

use Afterburner\Playbook\Tests\TestCase;

class PlaybookSystemAdminRouteTest extends TestCase
{
    public function test_regular_users_cannot_view_system_admin_playbook_pages(): void
    {
        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        $this->actingAs($user)
            ->get('/help/platform/audit-trail')
            ->assertNotFound();

        $this->actingAs($user)
            ->get('/help/platform/impersonation')
            ->assertNotFound();
    }
}
