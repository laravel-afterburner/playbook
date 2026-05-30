<?php

namespace Afterburner\Playbook\Tests\Feature;

use Afterburner\Playbook\Tests\TestCase;

class PlaybookRouteTest extends TestCase
{
    public function test_guests_cannot_access_playbook(): void
    {
        $this->get('/help')->assertRedirect('/login');
    }

    public function test_authenticated_users_are_redirected_to_default_page(): void
    {
        $user = $this->createVerifiedUser();

        $this->actingAs($user)
            ->get('/help')
            ->assertRedirect(route('playbook.show', [
                'section' => 'platform',
                'page' => 'welcome',
            ]));
    }

    public function test_unknown_page_returns_not_found(): void
    {
        $user = $this->createVerifiedUser();

        $this->actingAs($user)
            ->get('/help/platform/does-not-exist')
            ->assertNotFound();
    }

}
