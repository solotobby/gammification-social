@extends('general.master.apple')

@section('title', 'Terms & Conditions · Payhankey')
@section('meta_description', 'Payhankey terms and conditions for creators using the platform.')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Terms & Conditions',
    'eyebrow' => 'Legal',
    'title' => 'Terms &amp; Conditions',
    'lead' => 'Last updated ' . now()->format('F j, Y'),
    'compact' => true,
])

<section class="apl-section apl-section--white">
  <div class="apl-wrap">
    <article class="apl-legal reveal">
      <h2>1. Introduction</h2>
      <p>Welcome to Payhankey, operated by Payhankey Ltd, a digital platform for content creation and engagement. Payhankey Ltd operates as a subsidiary/affiliated platform under Freebyz Technologies Ltd. By accessing or using this platform, you agree to be bound by these Terms and Conditions.</p>

      <h2>2. Eligibility</h2>
      <p>By using Payhankey, you confirm that you are at least 18 years old (or have parental/guardian consent), provide accurate information, and will not create duplicate or misleading accounts.</p>

      <h2>3. User accounts</h2>
      <p>You are responsible for maintaining the confidentiality of your account credentials. All activities under your account remain your responsibility. We reserve the right to suspend accounts that violate our policies.</p>

      <h2>4. Content guidelines</h2>
      <p>You agree to post content that is original or properly licensed, complies with applicable laws, and does not contain harmful, misleading, or inappropriate material. We may review and remove content that violates these guidelines.</p>

      <h2>5. Platform use</h2>
      <p>You agree not to use bots, scripts, or automated systems; engage in spam or deceptive practices; or attempt to interfere with platform security or operations.</p>

      <h2>6. Engagement integrity</h2>
      <p>All user interactions must be genuine and organic. Artificial engagement, including third-party engagement services, is strictly prohibited.</p>

      <h2>7. Rewards &amp; participation</h2>
      <p>Payhankey may offer optional programs, campaigns, or rewards. Participation is voluntary, rewards are not guaranteed, and programs are subject to verification and compliance checks. Participation does not establish employment, partnership, or financial obligation.</p>

      <h2>8. Intellectual property</h2>
      <p>You retain ownership of your content. By posting, you grant Payhankey a non-exclusive license to use, display, and distribute your content on the platform for operational purposes.</p>

      <h2>9. Privacy</h2>
      <p>Your data is handled in accordance with our <a href="{{ url('/privacy/policy') }}">Privacy Policy</a>.</p>

      <h2>10. Suspension &amp; termination</h2>
      <p>We may suspend or terminate accounts that violate these Terms, engage in suspicious activity, or compromise platform integrity.</p>

      <h2>11. Limitation of liability</h2>
      <p>Payhankey is provided &ldquo;as is&rdquo; without warranties of uninterrupted service or specific earnings outcomes.</p>

      <h2>12. Updates</h2>
      <p>We may update these Terms at any time. Continued use of the platform indicates acceptance of updates.</p>

      <h2>13. Contact</h2>
      <p>Questions? Email <a href="mailto:{{ config('payhankey.support_email') }}">{{ config('payhankey.support_email') }}</a>.</p>
    </article>
  </div>
</section>
@endsection
