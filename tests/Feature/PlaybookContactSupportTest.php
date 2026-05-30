<?php

namespace Afterburner\Playbook\Tests\Feature;

use Afterburner\Playbook\Livewire\PlaybookContactSupport;
use Afterburner\Playbook\Notifications\PlaybookSupportRequestNotification;
use Afterburner\Playbook\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class PlaybookContactSupportTest extends TestCase
{
    public function test_users_can_submit_support_requests_to_system_administrators(): void
    {
        Notification::fake();

        $admin = $this->createVerifiedUser([
            'email' => 'admin@example.com',
            'is_system_admin' => true,
        ]);

        $user = $this->createVerifiedUser([
            'email' => 'member@example.com',
            'is_system_admin' => false,
        ]);

        Livewire::actingAs($user)
            ->test(PlaybookContactSupport::class)
            ->set('subject', 'Need help with invitations')
            ->set('message', 'I cannot find where to invite a new member.')
            ->call('submit')
            ->assertDispatched('banner-message', style: 'success', message: 'Your message has been sent. A system administrator will respond soon.');

        Notification::assertSentTo(
            $admin,
            PlaybookSupportRequestNotification::class,
            function (PlaybookSupportRequestNotification $notification) use ($user): bool {
                return $notification->sender->is($user)
                    && $notification->subject === 'Need help with invitations'
                    && str_contains($notification->message, 'invite a new member');
            }
        );
    }

    public function test_support_request_validation_requires_subject_and_message(): void
    {
        $user = $this->createVerifiedUser();

        Livewire::actingAs($user)
            ->test(PlaybookContactSupport::class)
            ->call('submit')
            ->assertHasErrors(['subject', 'message']);
    }

    public function test_support_request_shows_error_when_no_system_administrators_exist(): void
    {
        Notification::fake();

        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        Livewire::actingAs($user)
            ->test(PlaybookContactSupport::class)
            ->set('subject', 'Help')
            ->set('message', 'Something is broken.')
            ->call('submit')
            ->assertDispatched(
                'banner-message',
                style: 'danger',
                message: 'No system administrators are available to receive your message. Please try again later.'
            );

        Notification::assertNothingSent();
    }
}
