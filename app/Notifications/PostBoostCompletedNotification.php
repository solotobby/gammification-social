<?php

namespace App\Notifications;

use App\Models\PostBoost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostBoostCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PostBoost $boost
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clicks = number_format($this->boost->total_clicks);
        $analyticsUrl = url("post/timeline/{$this->boost->post_id}/analytics?tab=boost");

        return (new MailMessage)
            ->subject("🎯 Boost Campaign Completed! All {$clicks} Clicks Delivered")
            ->greeting("Hello {$notifiable->name},")
            ->line("Congratulations! Your boosted campaign has successfully delivered all {$clicks} guaranteed link clicks to your destination ({$this->boost->target_url}).")
            ->action('View Full Performance Report', $analyticsUrl)
            ->line('Your post has now returned to regular timeline status.')
            ->line('Thank you for advertising with Payhankey!');
    }

    public function toDatabase(object $notifiable): array
    {
        $clicks = number_format($this->boost->total_clicks);

        return [
            'title' => 'Boost Campaign Completed 🎯',
            'message' => "All {$clicks} guaranteed clicks have been delivered to your target destination!",
            'icon' => 'fa-check-circle text-success',
            'url' => url("post/timeline/{$this->boost->post_id}/analytics?tab=boost"),
            'post_id' => $this->boost->post_id,
            'boost_id' => $this->boost->id,
        ];
    }
}
