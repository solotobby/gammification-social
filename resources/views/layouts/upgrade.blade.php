<!-- Pricing Tables -->
<div class="content content-boxed overflow-hidden">

    @if (session()->has('success'))
        <div class="alert alert-success mb-2" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger mb-2" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="row py-5">

        @php
            $baseCurrency = userBaseCurrency(); // e.g. USD
            $currentLevel = userLevel(); // e.g. Basic, Creator, Influencer
            $disableBasic = in_array($currentLevel, ['Creator', 'Influencer']);
            
        @endphp

        @if ($baseCurrency !== 'NGN')

            <div class="row">
                @foreach (getLevels() as $level)
                    @php
                        $isActive = $currentLevel && $currentLevel === $level->name;
                        $isBasic = $level->name === 'Basic';
                        $isDisabled = $disableBasic && $isBasic;
                    @endphp

                    <div class="col-md-6 col-xl-4">

                        {{-- ACTIVE PLAN --}}
                        @if ($isActive)
                            <div class="block block-rounded block-themed text-center">
                                <div class="block-header bg-muted">
                                    <h3 class="block-title">{{ $level->name }}</h3>
                                </div>

                                <div class="block-content bg-body-light">
                                    <div class="py-2">
                                        <p class="h1 fw-bold mb-2">
                                            {{ getCurrencyCode() }}{{ convertToBaseCurrency($level->amount, auth()->user()->wallet->currency) }}
                                        </p>
                                        <p class="h6 text-muted">per month</p>
                                    </div>
                                </div>

                                <div class="block-content">
                                    <div class="py-2">
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->reg_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Upgrade Bonus
                                        </p>
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->ref_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Referral Bonus
                                        </p>

                                        @if ($level->name !== 'Basic')
                                            <p><strong>Account</strong> Monetization</p>
                                            <p><strong>Can make</strong> Withdrawals</p>
                                        @endif

                                        <p><strong>Email</strong> Support</p>
                                    </div>
                                </div>

                                <div class="block-content block-content-full bg-body-light">
                                    <span class="btn btn-secondary disabled px-4">
                                        <i class="fa fa-check me-1"></i> Active Plan
                                    </span>
                                </div>
                            </div>

                            {{-- UPGRADE / DISABLED PLANS --}}
                        @else
                            <div
                                class="block block-link-pop block-rounded text-center {{ $isDisabled ? 'opacity-50' : '' }}">

                                @if (!$isDisabled)
                                    <a href="{{ url('subscribe/' . $level->id) }}" style="color:black">
                                @endif

                                <div class="block-header">
                                    <h3 class="block-title">{{ $level->name }}</h3>
                                </div>

                                <div class="block-content bg-body-light">
                                    <div class="py-2">
                                        <p class="h1 fw-bold mb-2">
                                            {{ getCurrencyCode() }}{{ convertToBaseCurrency($level->amount, auth()->user()->wallet->currency) }}
                                        </p>
                                        <p class="h6 text-muted">per month</p>
                                    </div>
                                </div>

                                <div class="block-content">
                                    <div class="py-2">
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->reg_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Upgrade Bonus
                                        </p>
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->ref_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Referral Bonus
                                        </p>

                                        @if ($level->name !== 'Basic')
                                            <p><strong>Account</strong> Monetization</p>
                                            <p><strong>Can make</strong> Withdrawals</p>
                                        @endif

                                        <p><strong>Email</strong> Support</p>
                                    </div>
                                </div>

                                <div class="block-content block-content-full bg-body-light">
                                    <span
                                        class="btn btn-hero {{ $isDisabled ? 'btn-secondary disabled' : 'btn-primary' }} px-4">
                                        <i class="fa {{ $isDisabled ? 'fa-ban' : 'fa-arrow-up' }} me-1"></i>
                                        {{ $isDisabled ? 'Deactivated' : 'Upgrade' }}
                                    </span>
                                </div>

                                @if (!$isDisabled)
                                    </a>
                                @endif

                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @else
            @php
                $userActiveMode = $currentBillingMode ?? 'subscription';
                $currentMode = $currentMode ?? $userActiveMode;
                $discountRate = 0.1; // 10% off when paying via Direct Subscription
            @endphp

            {{-- Billing mode toggle --}}
            <div class="text-center mb-4">
                <div class="btn-group" role="group" aria-label="Billing mode toggle" id="billing-toggle">
                    <input type="radio" class="btn-check" name="billingMode" id="mode-subscription" autocomplete="off"
                        {{ $currentMode === 'subscription' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary px-4" for="mode-subscription">
                        <i class="fa fa-sync me-1"></i> Direct Subscription <span
                            class="badge bg-success text-white ms-1">Save 10%</span>
                    </label>

                    <input type="radio" class="btn-check" name="billingMode" id="mode-payg" autocomplete="off"
                        {{ $currentMode === 'payg' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary px-4" for="mode-payg">
                        <i class="fa fa-wallet me-1"></i> Pay As You Go
                    </label>
                </div>
                <p class="text-muted small mt-2 mb-0">
                    <strong>Direct Subscription</strong> renews automatically each month and gets a 10% discount.
                    <strong>Pay As You Go</strong> bills you monthly with no stored subscription — no discount, cancel
                    anytime.
                </p>
            </div>

            <div class="row">
                @foreach (getLevels() as $level)
                    @php
                        $isActive = $currentLevel && $currentLevel === $level->name;
                        $isBasic = $level->name === 'Basic';
                        $isDisabled = $disableBasic && $isBasic;

                        // Separate upgrade routes per billing mode. Adjust route names to match your app.
                        $subUrl = url('subscribe/' . $level->id);
                        $paygUrl = url('payg-subscribe/' . $level->id);
                        $currentUrl = $currentMode === 'payg' ? $paygUrl : $subUrl;

                        // Switch-billing-mode route for an already-active plan (same level, different billing type)
                        $switchToSubUrl = url('switch-billing-mode/' . $level->id . '?mode=subscription');
                        $switchToPaygUrl = url('switch-billing-mode/' . $level->id . '?mode=payg');

                        $basePrice = convertToBaseCurrency($level->amount, auth()->user()->wallet->currency);
                        $discountedPrice = convertToBaseCurrency(
                            $level->amount * (1 - $discountRate),
                            auth()->user()->wallet->currency,
                        );
                    @endphp

                    <div class="col-md-6 col-xl-4">

                        {{-- ACTIVE PLAN --}}
                        @if ($isActive)
                            <div class="block block-rounded block-themed text-center billing-card-active"
                                data-active-mode="{{ $userActiveMode }}">
                                <div class="block-header bg-muted">
                                    <h3 class="block-title">{{ $level->name }}</h3>
                                </div>

                                <div class="block-content bg-body-light">
                                    <div class="py-2 price-block">
                                        <p
                                            class="h3 fw-bold mb-1 price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">
                                            <span
                                                class="text-muted text-decoration-line-through small d-block">{{ getCurrencyCode() }}{{ number_format($basePrice, 2) }}</span>
                                            {{ getCurrencyCode() }}{{ number_format($discountedPrice, 2) }}

                                        </p>
                                        <span class="badge bg-success text-white align-top">-10%</span>
                                        <p
                                            class="h3 fw-bold mb-1 price-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">
                                            {{ getCurrencyCode() }}{{ number_format($basePrice, 2) }}
                                        </p>
                                        <p class="h6 text-muted">per month</p>
                                    </div>
                                </div>

                                <div class="block-content">
                                    <div class="py-2">
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->reg_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Upgrade Bonus
                                        </p>
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->ref_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Referral Bonus
                                        </p>

                                        @if ($level->name !== 'Basic')
                                            <p><strong>Account</strong> Monetization</p>
                                            <p><strong>Can make</strong> Withdrawals</p>
                                        @endif

                                        <p><strong>Email</strong> Support</p>
                                    </div>
                                </div>

                                <div class="block-content block-content-full bg-body-light">
                                    {{-- Shown when the selected toggle matches the plan's actual active billing mode --}}
                                    <span
                                        class="btn btn-secondary disabled px-4 active-plan-label {{ $currentMode === $userActiveMode ? '' : 'd-none' }}">
                                        <i class="fa fa-check me-1"></i> Active Plan
                                    </span>

                                    {{-- Shown when the user has toggled to a mode different from the plan's actual active mode --}}
                                    {{-- <a href="{{ $switchToSubUrl }}"
                                        class="btn btn-outline-primary px-4 switch-plan-link switch-to-subscription {{ $currentMode === 'subscription' && $userActiveMode !== 'subscription' ? '' : 'd-none' }}">
                                        <i class="fa fa-sync me-1"></i> Switch to Subscription
                                    </a>
                                    <a href="{{ $switchToPaygUrl }}"
                                        class="btn btn-outline-primary px-4 switch-plan-link switch-to-payg {{ $currentMode === 'payg' && $userActiveMode !== 'payg' ? '' : 'd-none' }}">
                                        <i class="fa fa-wallet me-1"></i> Switch to Pay As You Go
                                    </a> --}}
                                </div>
                            </div>

                            {{-- UPGRADE / DISABLED PLANS --}}
                        @else
                            <div class="block block-link-pop block-rounded text-center billing-card {{ $isDisabled ? 'opacity-50' : '' }}"
                                data-sub-url="{{ $subUrl }}" data-payg-url="{{ $paygUrl }}">

                                @if (!$isDisabled)
                                    <a href="{{ $currentUrl }}" class="billing-link" style="color:black">
                                @endif

                                <div class="block-header">
                                    <h3 class="block-title">{{ $level->name }}</h3>
                                </div>

                                <div class="block-content bg-body-light">
                                    <div class="py-2 price-block">
                                        <p
                                            class="h3 fw-bold mb-1 price-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">
                                            <span
                                                class="text-muted text-decoration-line-through small d-block">{{ getCurrencyCode() }}{{ number_format($basePrice) }}</span>
                                            {{ getCurrencyCode() }}{{ number_format($discountedPrice) }}

                                        </p>
                                        <span class="badge bg-success text-white align-top">-10%</span>
                                        <p
                                            class="h3 fw-bold mb-1 price-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">
                                            {{ getCurrencyCode() }}{{ number_format($basePrice) }}
                                        </p>
                                        <p class="h6 text-muted">per month</p>
                                    </div>
                                </div>

                                <div class="block-content">
                                    <div class="py-2">
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->reg_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Upgrade Bonus
                                        </p>
                                        <p>
                                            <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency($level->ref_bonus, auth()->user()->wallet->currency) }}</strong>
                                            Referral Bonus
                                        </p>

                                        @if ($level->name !== 'Basic')
                                            <p><strong>Account</strong> Monetization</p>
                                            <p><strong>Can make</strong> Withdrawals</p>
                                        @endif

                                        <p><strong>Email</strong> Support</p>
                                    </div>
                                </div>

                                <div class="block-content block-content-full bg-body-light">
                                    <span
                                        class="btn btn-hero {{ $isDisabled ? 'btn-secondary disabled' : 'btn-primary' }} px-4 billing-btn-label">
                                        <i class="fa {{ $isDisabled ? 'fa-ban' : 'fa-arrow-up' }} me-1"></i>
                                        @if ($isDisabled)
                                            Deactivated
                                        @else
                                            <span
                                                class="btn-text-subscription {{ $currentMode === 'subscription' ? '' : 'd-none' }}">Subscribe
                                                &amp; Save 10%</span>
                                            <span
                                                class="btn-text-payg {{ $currentMode === 'payg' ? '' : 'd-none' }}">Pay
                                                As You Go</span>
                                        @endif
                                    </span>
                                </div>

                                @if (!$isDisabled)
                                    </a>
                                @endif

                            </div>
                        @endif

                    </div>
                @endforeach
            </div>


            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var subRadio = document.getElementById('mode-subscription');
                    var paygRadio = document.getElementById('mode-payg');
                    var upgradeCards = document.querySelectorAll('.billing-card');
                    var activeCard = document.querySelector('.billing-card-active');

                    function applyMode(mode) {
                        // 1. Update upgrade links on non-active cards
                        upgradeCards.forEach(function(card) {
                            var link = card.querySelector('.billing-link');
                            if (link) {
                                link.href = mode === 'payg' ? card.dataset.paygUrl : card.dataset.subUrl;
                            }
                        });

                        // 2. Toggle price display (subscription discount vs payg full price) on ALL cards
                        document.querySelectorAll('.price-subscription').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'subscription');
                        });
                        document.querySelectorAll('.price-payg').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'payg');
                        });

                        // 3. Toggle button label text on upgrade cards
                        document.querySelectorAll('.btn-text-subscription').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'subscription');
                        });
                        document.querySelectorAll('.btn-text-payg').forEach(function(el) {
                            el.classList.toggle('d-none', mode !== 'payg');
                        });

                        // 4. Toggle the active-plan button between "Active Plan" and "Switch to X"
                        if (activeCard) {
                            var activeMode = activeCard.dataset.activeMode;
                            var label = activeCard.querySelector('.active-plan-label');
                            var toSub = activeCard.querySelector('.switch-to-subscription');
                            var toPayg = activeCard.querySelector('.switch-to-payg');

                            var matchesActive = (mode === activeMode);
                            if (label) label.classList.toggle('d-none', !matchesActive);
                            if (toSub) toSub.classList.toggle('d-none', !(mode === 'subscription' && activeMode !==
                                'subscription'));
                            if (toPayg) toPayg.classList.toggle('d-none', !(mode === 'payg' && activeMode !== 'payg'));
                        }
                    }

                    if (subRadio) {
                        subRadio.addEventListener('change', function() {
                            if (this.checked) applyMode('subscription');
                        });
                    }
                    if (paygRadio) {
                        paygRadio.addEventListener('change', function() {
                            if (this.checked) applyMode('payg');
                        });
                    }
                });
            </script>

        @endif



    </div>
</div>
<!-- END Pricing Tables -->



<!-- Special Offer -->
<div class="bg-body-light">
    <div class="content content-boxed content-full">
        <div class="py-5">
            <h2 class="mb-2 text-center">
                Special Offer
            </h2>
            <h3 class="fw-light text-muted push text-center">
                If you upgrade today you will also get all the following at no extra cost.
            </h3>
        </div>
        <div class="row py-3">
            <div class="col-sm-6 col-md-4 mb-5">
                <div class="my-3">
                    <i class="fa fa-2x fa-phone text-xeco"></i>
                </div>
                <h4 class="h5 mb-2">
                    Lifetime Support
                </h4>
                <p class="mb-0 text-muted">
                    Our high quality and award winning phone support will be available 24/7 and for as long as you are
                    using our service.
                </p>
            </div>
            <div class="col-sm-6 col-md-4 mb-5">
                <div class="my-3">
                    <i class="fa fa-2x fa-briefcase text-danger"></i>
                </div>
                <h4 class="h5 mb-2">
                    Unlimited Monetizable Posts
                </h4>
                <p class="mb-0 text-muted">
                    You will have unlimited posts where you can get paid on views, comments and likes. You can also post
                    longer contents.
                </p>
            </div>
            <div class="col-sm-6 col-md-4 mb-5">
                <div class="my-3">
                    <i class="fa fa-2x fa-photo-video text-xinspire"></i>
                </div>
                <h4 class="h5 mb-2">
                    Post Images & Videos
                </h4>
                <p class="mb-0 text-muted">
                    With your post, you will be able to posts images and videos that can be monetized
                </p>
            </div>
        </div>
    </div>
</div>
<!-- END Special Offer -->

<!-- Call to Action -->
<div class="content content-boxed text-center">
    <div class="py-5">
        <h2 class="mb-3 text-center">
            Why Upgrade?
        </h2>
        <h3 class="h4 fw-light text-muted push text-center">
            Upgrading your account can help you expand your reach and acquire much more followers!
        </h3>
        {{-- <span class="m-2 d-inline-block">
              <a class="btn btn-hero btn-primary" href="javascript:void(0)" data-toggle="click-ripple">
                <i class="fa fa-link opacity-50 me-1"></i> Learn How..
              </a>
            </span> --}}
    </div>
</div>
