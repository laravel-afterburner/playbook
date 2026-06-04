<?php

namespace Afterburner\Playbook\Tests\Feature;

use Afterburner\Playbook\Livewire\PlaybookFaqSection;
use Afterburner\Playbook\Models\PlaybookFaq;
use Afterburner\Playbook\Tests\TestCase;
use Livewire\Livewire;

class PlaybookFaqTest extends TestCase
{
    public function test_authenticated_users_can_access_faq_route(): void
    {
        $user = $this->createVerifiedUser();

        PlaybookFaq::query()->create([
            'question' => 'How do I invite a member?',
            'answer' => 'Go to Entity settings and use the Invitations tab.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($user);

        $this->assertSame('/help/faq', route('playbook.faq', [], false));

        Livewire::test(PlaybookFaqSection::class)
            ->assertSee('How do I invite a member?');
    }

    public function test_guests_cannot_access_faq_page(): void
    {
        $this->get('/help/faq')->assertRedirect('/login');
    }

    public function test_regular_users_cannot_access_faq_page_without_published_faqs(): void
    {
        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        $this->actingAs($user)
            ->get('/help/faq')
            ->assertForbidden();
    }

    public function test_system_admins_can_access_faq_page_without_published_faqs(): void
    {
        $admin = $this->createVerifiedUser(['is_system_admin' => true]);

        Livewire::actingAs($admin)
            ->test(PlaybookFaqSection::class)
            ->assertSee('No FAQs yet');
    }

    public function test_users_only_see_published_faqs(): void
    {
        PlaybookFaq::query()->create([
            'question' => 'Published question',
            'answer' => 'Published answer',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        PlaybookFaq::query()->create([
            'question' => 'Draft question',
            'answer' => 'Draft answer',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        Livewire::actingAs($user)
            ->test(PlaybookFaqSection::class)
            ->assertSee('Published question')
            ->assertDontSee('Draft question');
    }

    public function test_system_admins_can_manage_faqs(): void
    {
        $admin = $this->createVerifiedUser(['is_system_admin' => true]);

        Livewire::actingAs($admin)
            ->test(PlaybookFaqSection::class)
            ->call('openCreateModal')
            ->set('question', 'How do I reset my password?')
            ->set('answer', 'Use the forgot password link on the sign-in page.')
            ->set('isPublished', true)
            ->call('saveFaq')
            ->assertDispatched('banner-message', style: 'success', message: 'FAQ created successfully.');

        $this->assertDatabaseHas('playbook_faqs', [
            'question' => 'How do I reset my password?',
            'is_published' => true,
        ]);
    }

    public function test_system_admins_can_update_and_delete_faqs(): void
    {
        $admin = $this->createVerifiedUser(['is_system_admin' => true]);

        $faq = PlaybookFaq::query()->create([
            'question' => 'Original question',
            'answer' => 'Original answer',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(PlaybookFaqSection::class)
            ->call('openEditModal', $faq->id)
            ->set('question', 'Updated question')
            ->set('answer', 'Updated answer')
            ->set('isPublished', false)
            ->call('saveFaq')
            ->assertDispatched('banner-message', style: 'success', message: 'FAQ updated successfully.');

        $this->assertDatabaseHas('playbook_faqs', [
            'id' => $faq->id,
            'question' => 'Updated question',
            'is_published' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(PlaybookFaqSection::class)
            ->call('confirmDeletion', $faq->id)
            ->call('deleteFaq')
            ->assertDispatched('banner-message', style: 'success', message: 'FAQ deleted successfully.');

        $this->assertDatabaseMissing('playbook_faqs', ['id' => $faq->id]);
    }

    public function test_system_admins_see_draft_faqs(): void
    {
        PlaybookFaq::query()->create([
            'question' => 'Draft question',
            'answer' => 'Draft answer',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $admin = $this->createVerifiedUser(['is_system_admin' => true]);

        Livewire::actingAs($admin)
            ->test(PlaybookFaqSection::class)
            ->assertSee('Draft question')
            ->assertSee('Draft');
    }

    public function test_regular_users_cannot_create_faqs(): void
    {
        PlaybookFaq::query()->create([
            'question' => 'Published question',
            'answer' => 'Published answer',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $user = $this->createVerifiedUser(['is_system_admin' => false]);

        Livewire::actingAs($user)
            ->test(PlaybookFaqSection::class)
            ->call('openCreateModal')
            ->assertForbidden();
    }

    public function test_faq_validation_requires_question_and_answer(): void
    {
        $admin = $this->createVerifiedUser(['is_system_admin' => true]);

        Livewire::actingAs($admin)
            ->test(PlaybookFaqSection::class)
            ->call('openCreateModal')
            ->call('saveFaq')
            ->assertHasErrors(['question', 'answer']);
    }

    public function test_system_admins_can_reorder_faqs(): void
    {
        $admin = $this->createVerifiedUser(['is_system_admin' => true]);

        $first = PlaybookFaq::query()->create([
            'question' => 'First',
            'answer' => 'First answer',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $second = PlaybookFaq::query()->create([
            'question' => 'Second',
            'answer' => 'Second answer',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(PlaybookFaqSection::class)
            ->call('moveFaq', $second->id, 'up');

        $this->assertSame(2, $first->fresh()->sort_order);
        $this->assertSame(1, $second->fresh()->sort_order);
    }
}
