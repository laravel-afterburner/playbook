<?php

namespace Afterburner\Playbook\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlaybookSupportRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Authenticatable $sender,
        public string $subject,
        public string $message,
        public string $contextUrl,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Support request: {$this->subject}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->sender->name} ({$this->sender->email}) submitted a support request from Help & Support.")
            ->line("Subject: {$this->subject}")
            ->line($this->message)
            ->action('View page', $this->contextUrl);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'playbook_support_request',
            'message' => "{$this->sender->name} submitted a support request: {$this->subject}",
            'sender_name' => $this->sender->name,
            'sender_email' => $this->sender->email,
            'subject' => $this->subject,
            'body' => $this->message,
            'context_url' => $this->contextUrl,
        ];
    }
}
