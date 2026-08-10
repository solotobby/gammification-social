@extends('general.master.apple')

@section('title', 'Privacy Policy · Payhankey')
@section('meta_description', 'Payhankey privacy policy — how we collect, use, and protect your data.')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Privacy Policy',
    'eyebrow' => 'Legal',
    'title' => 'Privacy Policy',
    'lead' => 'Last updated ' . now()->format('F j, Y'),
    'compact' => true,
])

<section class="apl-section apl-section--white">
  <div class="apl-wrap">
    <article class="apl-legal reveal">
      <p>At Payhankey, accessible from <a href="https://payhankey.com">https://payhankey.com</a>, one of our main priorities is the privacy of our visitors and creators. This Privacy Policy explains what information we collect, how we use it, and your rights.</p>
      <p>This policy applies to our online activities and is valid for visitors to our website and registered users of the platform. It does not apply to information collected offline or via channels other than this website.</p>

      <h2>Consent</h2>
      <p>By using our website or creating an account, you consent to this Privacy Policy and agree to its terms.</p>

      <h2>Information we collect</h2>
      <p>The personal information you are asked to provide — and why — will be made clear at the point we ask for it. When you register, we may collect name, username, email address, and payout details. If you contact us directly, we may receive additional information such as the contents of your message and attachments.</p>

      <h2>How we use your information</h2>
      <ul>
        <li>Provide, operate, and maintain the platform</li>
        <li>Process earnings, payouts, and subscriptions</li>
        <li>Improve, personalize, and expand our services</li>
        <li>Communicate with you about your account, updates, and support</li>
        <li>Detect, prevent, and address fraud or abuse</li>
        <li>Comply with legal obligations</li>
      </ul>

      <h2>Log files</h2>
      <p>Payhankey follows standard log file practices. These logs may include IP addresses, browser type, ISP, date/time stamps, referring pages, and click counts. This data is not linked to personally identifiable information and is used for analytics, administration, and security.</p>

      <h2>Cookies</h2>
      <p>Like most websites, we use cookies to store preferences and improve your experience. You can disable cookies through your browser settings.</p>

      <h2>Third-party services</h2>
      <p>We use payment processors and analytics providers that may collect information according to their own privacy policies. Payhankey does not control third-party cookies used by advertising partners.</p>

      <h2>Your data protection rights</h2>
      <p>Depending on your location, you may have the right to access, correct, delete, or restrict processing of your personal data, or to request portability. Contact us at <a href="mailto:{{ config('payhankey.support_email') }}">{{ config('payhankey.support_email') }}</a> to exercise these rights.</p>

      <h2>Children's information</h2>
      <p>Payhankey does not knowingly collect personal information from children under 13. If you believe a child provided personal data on our platform, contact us and we will promptly remove it.</p>

      <h2>Contact</h2>
      <p>Questions about this policy? Email <a href="mailto:{{ config('payhankey.support_email') }}">{{ config('payhankey.support_email') }}</a> or use our <a href="{{ url('/contact') }}">contact form</a>.</p>
    </article>
  </div>
</section>
@endsection
