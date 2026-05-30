<?php

namespace Afterburner\Playbook\Tests\Unit;

use Afterburner\Playbook\Support\SystemAdminRecipients;
use Afterburner\Playbook\Tests\TestCase;

class SystemAdminRecipientsTest extends TestCase
{
    public function test_returns_only_system_administrators(): void
    {
        $admin = $this->createVerifiedUser([
            'email' => 'admin@example.com',
            'is_system_admin' => true,
        ]);

        $this->createVerifiedUser([
            'email' => 'user@example.com',
            'is_system_admin' => false,
        ]);

        $recipients = SystemAdminRecipients::all();

        $this->assertCount(1, $recipients);
        $this->assertTrue($recipients->first()->is($admin));
    }

    public function test_returns_empty_collection_when_no_system_administrators_exist(): void
    {
        $this->createVerifiedUser(['is_system_admin' => false]);

        $this->assertTrue(SystemAdminRecipients::all()->isEmpty());
    }
}
