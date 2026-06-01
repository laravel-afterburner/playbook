<?php

namespace Afterburner\Playbook\Tests\Unit;

use Afterburner\Playbook\Models\PlaybookFaq;
use Afterburner\Playbook\Support\PlaybookFaqNavigation;
use Afterburner\Playbook\Tests\TestCase;

class PlaybookFaqNavigationTest extends TestCase
{
    public function test_regular_users_do_not_see_faq_navigation_without_published_faqs(): void
    {
        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        $this->assertFalse(PlaybookFaqNavigation::isVisible($user));
    }

    public function test_regular_users_see_faq_navigation_when_published_faqs_exist(): void
    {
        PlaybookFaq::query()->create([
            'question' => 'Published question',
            'answer' => 'Published answer',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        $this->assertTrue(PlaybookFaqNavigation::isVisible($user));
    }

    public function test_regular_users_do_not_see_faq_navigation_when_only_drafts_exist(): void
    {
        PlaybookFaq::query()->create([
            'question' => 'Draft question',
            'answer' => 'Draft answer',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        $this->assertFalse(PlaybookFaqNavigation::isVisible($user));
    }

    public function test_system_admins_always_see_faq_navigation(): void
    {
        $admin = $this->createVerifiedUser(['is_system_admin' => true]);

        $this->assertTrue(PlaybookFaqNavigation::isVisible($admin));
    }
}
