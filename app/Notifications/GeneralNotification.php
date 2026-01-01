<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    public array $data;
    public bool $sendMail;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data, bool $sendMail = false)
    {
        $this->data = $data;
        $this->sendMail = $sendMail;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {

        return $this->sendMail
            ? ['database', 'mail']
            : ['database'];

        // return [
        //     'database',
        //     // 'mail'
        // ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
            ->subject($this->data['title'] ?? 'Notification')
            ->line($this->data['message'] ?? 'You have a new notification.')
            ->action('View', $this->data['url'] ?? url('/'))
            ->line('Thank you for using our application.');
            
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {

        return [
            'title'   => $this->data['title'] ?? 'Notification',
            'message' => $this->data['message'] ?? null,
            'icon'    => $this->data['icon'] ?? 'fa-bell text-primary',
            'url'     => $this->data['url'] ?? '#',
        ];

        // return [
        //     'title' => 'New post',
        //     'message' => 'Someone you follow just posted.',
        //     'url' => url('/feed'),
        // ];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
