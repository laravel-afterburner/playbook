<?php

namespace Afterburner\Playbook\Livewire;

use Afterburner\Playbook\Models\PlaybookFaq;
use App\Traits\InteractsWithBanner;
use Afterburner\Playbook\Support\PlaybookPermissions;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class PlaybookFaqSection extends Component
{
    use InteractsWithBanner;

    public bool $showFormModal = false;

    public bool $confirmingDeletion = false;

    public ?int $editingFaqId = null;

    public ?int $deletingFaqId = null;

    public string $question = '';

    public string $answer = '';

    public bool $isPublished = true;

    public function openCreateModal(): void
    {
        abort_unless($this->canManage(), 403);

        $this->resetForm();
        $this->editingFaqId = null;
        $this->showFormModal = true;
    }

    public function openEditModal(int $faqId): void
    {
        abort_unless($this->canManage(), 403);

        $faq = $this->findFaq($faqId);

        $this->editingFaqId = $faq->id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->isPublished = $faq->is_published;
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function saveFaq(): void
    {
        abort_unless($this->canManage(), 403);

        $validated = $this->validate($this->rules());

        $attributes = [
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'is_published' => $validated['isPublished'],
        ];

        if ($this->editingFaqId) {
            $faq = $this->findFaq($this->editingFaqId);
            $faq->update($attributes);
            $this->banner('FAQ updated successfully.');
        } else {
            PlaybookFaq::query()->create([
                ...$attributes,
                'sort_order' => (int) PlaybookFaq::query()->max('sort_order') + 1,
            ]);
            $this->banner('FAQ created successfully.');
        }

        $this->closeFormModal();
    }

    public function confirmDeletion(int $faqId): void
    {
        abort_unless($this->canManage(), 403);

        $this->deletingFaqId = $faqId;
        $this->confirmingDeletion = true;
    }

    public function cancelDeletion(): void
    {
        $this->confirmingDeletion = false;
        $this->deletingFaqId = null;
    }

    public function deleteFaq(): void
    {
        abort_unless($this->canManage(), 403);

        if ($this->deletingFaqId) {
            $this->findFaq($this->deletingFaqId)->delete();
            $this->banner('FAQ deleted successfully.');
        }

        $this->cancelDeletion();
    }

    public function moveFaq(int $faqId, string $direction): void
    {
        abort_unless($this->canManage(), 403);

        $faqs = PlaybookFaq::query()->ordered()->get();
        $index = $faqs->search(fn (PlaybookFaq $faq) => $faq->id === $faqId);

        if ($index === false) {
            return;
        }

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($swapIndex < 0 || $swapIndex >= $faqs->count()) {
            return;
        }

        $current = $faqs[$index];
        $neighbor = $faqs[$swapIndex];

        $currentOrder = $current->sort_order;
        $current->update(['sort_order' => $neighbor->sort_order]);
        $neighbor->update(['sort_order' => $currentOrder]);
    }

    public function canManage(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return PlaybookPermissions::canManageFaqs($user, $user->currentTeam);
    }

    public function mount(): void
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 403);
        abort_unless(PlaybookPermissions::canViewFaqs($user, $user->currentTeam), 403);
    }

    /**
     * @return Collection<int, PlaybookFaq>
     */
    public function getFaqsProperty(): Collection
    {
        $query = PlaybookFaq::query()->ordered();

        if (! $this->canManage()) {
            $query->published();
        }

        return $query->get();
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:10000'],
            'isPublished' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'question' => 'question',
            'answer' => 'answer',
            'isPublished' => 'published status',
        ];
    }

    protected function resetForm(): void
    {
        $this->resetValidation();
        $this->editingFaqId = null;
        $this->question = '';
        $this->answer = '';
        $this->isPublished = true;
    }

    protected function findFaq(int $faqId): PlaybookFaq
    {
        return PlaybookFaq::query()->findOrFail($faqId);
    }

    public function render()
    {
        return view('afterburner-playbook::livewire.playbook-faq-section');
    }
}
