<?php

namespace Afterburner\Playbook\Livewire;

use Afterburner\Playbook\Notifications\PlaybookSupportRequestNotification;
use App\Traits\InteractsWithBanner;
use Afterburner\Playbook\Support\SystemAdminRecipients;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class PlaybookContactSupport extends Component
{
    use InteractsWithBanner;

    public bool $showModal = false;

    public string $subject = '';

    public string $message = '';

    public function openModal(): void
    {
        $this->resetValidation();
        $this->showModal = true;
    }

    public function submit(): void
    {
        $validated = $this->validate($this->rules());

        $admins = SystemAdminRecipients::all();

        if ($admins->isEmpty()) {
            $this->dangerBanner('No system administrators are available to receive your message. Please try again later.');

            return;
        }

        $sender = auth()->user();
        $contextUrl = request()->header('Referer', url()->current());

        Notification::send(
            $admins,
            new PlaybookSupportRequestNotification(
                sender: $sender,
                subject: $validated['subject'],
                message: $validated['message'],
                contextUrl: $contextUrl,
            ),
        );

        $this->showModal = false;
        $this->reset(['subject', 'message']);
        $this->banner('Your message has been sent. A system administrator will respond soon.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:'.(int) config('afterburner-playbook.contact_support.subject_max_length', 255)],
            'message' => ['required', 'string', 'max:'.(int) config('afterburner-playbook.contact_support.message_max_length', 5000)],
        ];
    }

    public function render()
    {
        return view('afterburner-playbook::livewire.playbook-contact-support');
    }
}
