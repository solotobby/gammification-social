@extends('general.master.apple')

@section('title', 'Child Safety · Payhankey')
@section('meta_description', 'Payhankey child safety standards and reporting contact for Child Sexual Abuse and Exploitation (CSAE) concerns.')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Child Safety',
    'eyebrow' => 'Legal',
    'title' => 'Child Safety',
    'lead' => 'Standards, reporting, and contact for child safety concerns.',
    'compact' => true,
])

<section class="apl-section apl-section--white">
  <div class="apl-wrap">
    <article class="apl-legal reveal">
      <p class="apl-lead" style="font-size:1.05rem;font-weight:600;margin-bottom:1.5rem;">
        Payhankey: Monetize Content prohibits Child Sexual Abuse and Exploitation (CSAE).
      </p>

      <p>Payhankey is committed to keeping our platform safe. We have zero tolerance for child sexual abuse material (CSAM), grooming, exploitation, or any content that sexualizes minors. Accounts involved in CSAE may be permanently removed and reported to the appropriate authorities.</p>

      <h2>Our standards</h2>
      <ul>
        <li>Users must be at least 18 years old (or meet the minimum age required in their jurisdiction).</li>
        <li>Content depicting, promoting, or facilitating the abuse or exploitation of children is strictly prohibited.</li>
        <li>We investigate reports promptly and cooperate with law enforcement when required.</li>
      </ul>

      <h2>Report a child safety concern</h2>
      <p>If you believe content on Payhankey involves child sexual abuse or exploitation, or if you have an urgent child safety concern, contact our designated child safety point of contact:</p>

      <ul>
        <li><strong>Designated contact:</strong> {{ config('payhankey.child_safety_contact') }}</li>
        <li><strong>Email:</strong> <a href="mailto:{{ config('payhankey.child_safety_email') }}">{{ config('payhankey.child_safety_email') }}</a></li>
      </ul>

      <p>Please include as much detail as possible (links, usernames, dates, and a description of the concern) so we can investigate quickly. For immediate danger to a child, contact your local emergency services first.</p>

      <h2>Other support</h2>
      <p>For general account or platform questions unrelated to child safety, email <a href="mailto:{{ config('payhankey.support_email') }}">{{ config('payhankey.support_email') }}</a> or use our <a href="{{ url('/contact') }}">contact form</a>.</p>
    </article>
  </div>
</section>
@endsection
