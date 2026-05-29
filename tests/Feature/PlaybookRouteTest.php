<?php

namespace Afterburner\Playbook\Tests\Feature;

use Afterburner\Playbook\Tests\TestCase;
use App\Models\User;

class PlaybookRouteTest extends TestCase
{
    public function test_guests_cannot_access_playbook(): void
    {
        $this->get('/playbook')->assertRedirect('/login');
    }

    public function test_authenticated_users_are_redirected_to_default_page(): void
    {
        $user = $this->createVerifiedUser();

        $this->actingAs($user)
            ->get('/playbook')
            ->assertRedirect(route('playbook.show', [
                'section' => 'platform',
                'page' => 'welcome',
            ]));
    }

    public function test_unknown_page_returns_not_found(): void
    {
        $user = $this->createVerifiedUser();

        $this->actingAs($user)
            ->get('/playbook/platform/does-not-exist')
            ->assertNotFound();
    }
}
