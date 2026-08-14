@extends('general.master.apple')

@section('title', 'Payhankey FAQ: Creator Monetization, Earnings & Payouts')
@section('meta_description', 'Learn how Payhankey works, how creators earn money, Payhankey Rolls, creator subscriptions, communities, withdrawals, AI tools and monetization in Africa.')

@php
    $faqCategories = [
        [
            'title' => 'About Payhankey',
            'items' => [
                [
                    'q' => 'What is Payhankey?',
                    'a' => 'Payhankey is an AI-powered creator platform built to help creators grow their audience, monetize their content, build communities and create sustainable digital businesses. You can publish posts and Payhankey Rolls, create communities, earn from eligible monetization programs and receive payouts through supported payment methods.',
                ],
                [
                    'q' => 'Who can join Payhankey?',
                    'a' => 'Payhankey is built for creators and digital professionals across Africa and beyond. Whether you\'re a student, influencer, educator, entrepreneur, musician, comedian, writer or simply someone with something valuable to share, you can create an account and start building your audience. Availability of specific features and payout methods may vary by country.',
                ],
            ],
        ],
        [
            'title' => 'Earning Money on Payhankey',
            'items' => [
                [
                    'q' => 'How do I earn money on Payhankey?',
                    'a' => 'Creators can earn through eligible content monetization, creator rewards, community memberships, referrals and other monetization opportunities available on the platform. Your available earning options depend on your account plan, eligibility and the specific program.',
                ],
                [
                    'q' => 'Do I need a certain number of followers to earn on Payhankey?',
                    'a' => 'Payhankey is designed to give creators opportunities to monetize without requiring the large follower counts demanded by many traditional creator monetization programs. However, specific monetization features may have their own eligibility requirements.',
                ],
                [
                    'q' => 'Does Payhankey pay for views?',
                    'a' => 'Eligible content monetization programs may factor in engagement such as views, likes and comments, depending on your account plan and the specific program rules. Availability, rates and eligibility can vary — check your dashboard for the monetization options available to you.',
                ],
            ],
        ],
        [
            'title' => 'Creator Plans & Subscriptions',
            'items' => [
                [
                    'q' => 'How much does Payhankey cost?',
                    'a' => 'Creating a basic Payhankey account is free. Creators can optionally subscribe to the Creator plan for $1/month or the Influencer plan for $5/month to access additional creator, monetization and visibility features.',
                ],
                [
                    'q' => 'How do Payhankey creator subscriptions work?',
                    'a' => 'The Creator plan costs $1/month and the Influencer plan costs $5/month. Subscriptions unlock additional creator features, monetization opportunities and visibility. You can manage your subscription from your account and cancel according to the applicable subscription terms.',
                ],
            ],
        ],
        [
            'title' => 'Payouts & Withdrawals',
            'items' => [
                [
                    'q' => 'How do I get paid on Payhankey?',
                    'a' => 'Payhankey supports payout options including local bank accounts, PayPal and USDT, depending on your country and the payment methods available to you. Your available payout options are shown in your account.',
                ],
            ],
        ],
        [
            'title' => 'Payhankey Rolls',
            'items' => [
                [
                    'q' => 'What are Payhankey Rolls?',
                    'a' => 'Payhankey Rolls are Payhankey\'s short-form vertical videos. Creators can use Rolls to share entertaining, educational or informative content and reach new audiences through Payhankey\'s discovery experience.',
                ],
            ],
        ],
        [
            'title' => 'Communities',
            'items' => [
                [
                    'q' => 'What are Payhankey Communities?',
                    'a' => 'Communities let creators build dedicated spaces around their audience, interests or expertise. A creator can create a Public, Membership, Private or Request-to-Join community and, where supported, charge members for access to exclusive content and experiences.',
                ],
                [
                    'q' => 'Can I make money from my Payhankey community?',
                    'a' => 'Yes. Creators can create paid communities, set a membership price and earn recurring income from subscribers. Payhankey currently charges a 1–2% platform fee on eligible community earnings, subject to the applicable terms and payment processing arrangements.',
                ],
            ],
        ],
        [
            'title' => 'Payhankey and Other Platforms',
            'items' => [
                [
                    'q' => 'Is Payhankey better than TikTok for monetization?',
                    'a' => 'Payhankey and TikTok are built around different priorities. TikTok is a global entertainment and discovery platform, while Payhankey is being built specifically around creator monetization, communities and creator businesses, with a strong focus on African creators and local payment infrastructure. You can use both platforms and build your audience across them.',
                ],
                [
                    'q' => 'Is Payhankey like Facebook?',
                    'a' => 'Payhankey has social features similar to other social platforms, but its focus is different. Payhankey is designed around helping creators monetize content, build communities and develop recurring income streams, particularly within the African creator economy.',
                ],
                [
                    'q' => 'Can I use Payhankey and TikTok together?',
                    'a' => 'Absolutely. Payhankey does not require creators to abandon the platforms they already use. You can use TikTok, Instagram, YouTube, Facebook and other platforms to grow your audience while using Payhankey to build additional monetization and community opportunities.',
                ],
            ],
        ],
    ];

    $schemaEntities = [];
    foreach ($faqCategories as $category) {
        foreach ($category['items'] as $item) {
            $schemaEntities[] = [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['a'],
                ],
            ];
        }
    }
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $schemaEntities,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'FAQ',
    'title' => 'Everything Creators Need to Know',
    'lead' => 'Looking for answers about Payhankey? Learn how creator monetization works, how to earn from content and communities, how Payhankey Rolls work, how subscriptions work, and how creators receive their payouts.',
])

<section class="apl-faq-shell">
  <div class="apl-wrap apl-faq-page">
    <nav class="apl-faq-toc reveal" aria-label="FAQ categories">
      @foreach ($faqCategories as $i => $category)
        <a href="#{{ \Illuminate\Support\Str::slug($category['title']) }}">
          <span class="apl-faq-toc__num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
          {{ $category['title'] }}
        </a>
      @endforeach
    </nav>

    <div class="apl-faq-stack">
      @foreach ($faqCategories as $i => $category)
        <section class="apl-faq-cat reveal" id="{{ \Illuminate\Support\Str::slug($category['title']) }}">
          <header class="apl-faq-cat__head">
            <span class="apl-faq-cat__index">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
            <h2>{{ $category['title'] }}</h2>
          </header>
          <div class="apl-faq-cat__body">
            @foreach ($category['items'] as $item)
              <article class="apl-faq-entry">
                <h3>{{ $item['q'] }}</h3>
                <p>{{ $item['a'] }}</p>
              </article>
            @endforeach
          </div>
        </section>
      @endforeach
    </div>
  </div>
</section>

@include('general.partials.apl-close-cta')
@endsection
