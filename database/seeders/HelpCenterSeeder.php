<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HelpCenterSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Accounts',
            'Payments',
            'Communities',
            'Subscriptions',
            'Verification',
            'AI',
            'Policies',
            'Security',
        ];

        $categoryMap = [];
        foreach ($categories as $index => $name) {
            $category = HelpCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $index + 1]
            );
            $categoryMap[$name] = $category;
        }

        $articles = [
            ['Accounts', 'What is Payhankey?', 'Payhankey is an AI-powered creator platform built to help creators grow their audience, monetize their content, build communities and create sustainable digital businesses. You can publish posts and Payhankey Rolls, create communities, earn from eligible monetization programs and receive payouts through supported payment methods.'],
            ['Accounts', 'Who can join Payhankey?', 'Payhankey is built for creators and digital professionals across Africa and beyond. Whether you\'re a student, influencer, educator, entrepreneur, musician, comedian, writer or simply someone with something valuable to share, you can create an account and start building your audience. Availability of specific features and payout methods may vary by country.'],
            ['Payments', 'How do I get paid on Payhankey?', 'Payhankey supports payout options including local bank accounts, PayPal and USDT, depending on your country and the payment methods available to you. Your available payout options are shown in your account.'],
            ['Payments', 'Does Payhankey pay for views?', 'Eligible content monetization programs may factor in engagement such as views, likes and comments, depending on your account plan and the specific program rules. Availability, rates and eligibility can vary — check your dashboard for the monetization options available to you.'],
            ['Communities', 'What are Payhankey Communities?', 'Communities let creators build dedicated spaces around their audience, interests or expertise. A creator can create a Public, Membership, Private or Request-to-Join community and, where supported, charge members for access to exclusive content and experiences.'],
            ['Communities', 'Can I make money from my Payhankey community?', 'Yes. Creators can create paid communities, set a membership price and earn recurring income from subscribers. Payhankey currently charges a 1–2% platform fee on eligible community earnings, subject to the applicable terms and payment processing arrangements.'],
            ['Subscriptions', 'How much does Payhankey cost?', 'Creating a basic Payhankey account is free. Creators can optionally subscribe to the Creator plan for $1/month or the Influencer plan for $5/month to access additional creator, monetization and visibility features.'],
            ['Subscriptions', 'How do Payhankey creator subscriptions work?', 'The Creator plan costs $1/month and the Influencer plan costs $5/month. Subscriptions unlock additional creator features, monetization opportunities and visibility. You can manage your subscription from your account and cancel according to the applicable subscription terms.'],
            ['Verification', 'How do I verify my Payhankey account?', 'Verification helps confirm your identity and unlocks eligible payout and monetization features. Open your account settings, follow the verification steps shown for your country, and submit the requested details. Review timelines can vary.'],
            ['AI', 'What AI tools does Payhankey offer?', 'Payhankey includes AI-assisted creator tools designed to help you ideate, draft, refine and publish content faster. Available tools depend on your plan and may expand over time — check the AI Tools section in the product for what is currently enabled on your account.'],
            ['Policies', 'Where can I find Payhankey policies?', 'Payhankey\'s Terms & Conditions and Privacy Policy are available from the website footer and account menus. For platform rules that affect monetization, communities or content, review the in-product notices and applicable program terms shown in your dashboard.'],
            ['Security', 'How does Payhankey protect my account?', 'Use a strong unique password, keep your login details private, and enable any available security options on your account. Payhankey will never ask for your password by email. If you notice unusual activity, change your password and contact support immediately.'],
        ];

        foreach ($articles as $index => [$categoryName, $title, $body]) {
            $category = $categoryMap[$categoryName];
            HelpArticle::query()->firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'help_category_id' => $category->id,
                    'title' => $title,
                    'body' => $body,
                    'meta_description' => Str::limit($body, 155),
                    'published' => true,
                    'sort_order' => $index + 1,
                    'published_at' => now(),
                ]
            );
        }
    }
}
