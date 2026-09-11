<?php

namespace App\Notifications;

use App\Models\PostBoost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostBoostedNotification extends Notification
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
        $costPk = number_format($this->boost->pk_cost);
        $costNgn = number_format($this->boost->pk_cost * 10);
        $analyticsUrl = url("post/timeline/{$this->boost->post_id}/analytics?tab=boost");

        return (new MailMessage)
            ->subject("🚀 Your Post Boost is Live! ({$clicks} Guaranteed Clicks)")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your post has been successfully boosted and is now actively distributed across {$this->getPlatformText()}.")
            ->line("**Campaign Details:**")
            ->line("• **Target Destination:** {$this->boost->target_url}")
            ->line("• **Call-to-Action:** {$this->boost->cta}")
            ->line("• **Guaranteed Clicks:** {$clicks} clicks")
            ->line("• **Total Investment:** {$costPk} PayKoin (₦{$costNgn})")
            ->line("• **Rate:** 3 PayKoin (₦30) per verified click")
            ->action('Track Live Campaign Analytics', $analyticsUrl)
            ->line('Note: Standard creator engagement monetization (view/like earnings) is temporarily paused for this post while the boost is active.')
            ->line('Thank you for advertising with Payhankey!');
    }

    public function toDatabase(object $notifiable): array
    {
        $clicks = number_format($this->boost->total_clicks);
        $costPk = number_format($this->boost->pk_cost);

        return [
            'title' => 'Post Boost Activated 🚀',
            'message' => "Your post is now boosted for {$clicks} guaranteed clicks ({$costPk} PK).",
            'icon' => 'fa-rocket text-primary',
            'url' => url("post/timeline/{$this->boost->post_id}/analytics?tab=boost"),
            'post_id' => $this->boost->post_id,
            'boost_id' => $this->boost->id,
        ];
    }

    protected function getPlatformText(): string
    {
        if ($this->boost->platform_payhankey && $this->boost->platform_partner) {
            return 'Payhankey Feed and Partner Websites';
        } elseif ($this->boost->platform_payhankey) {
            return 'Payhankey Feed';
        } elseif ($this->boost->platform_partner) {
            return 'Partner Websites';
        }
        return 'Selected Networks';
    }
}
