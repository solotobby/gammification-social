@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-grid--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .dash-kpi {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            box-shadow: var(--dash-shadow);
            height: 100%;
        }

        .dash-kpi__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dash-kpi__icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1rem;
        }

        .dash-kpi__icon--indigo { background: #eef2ff; color: #4f46e5; }
        .dash-kpi__icon--emerald { background: #ecfdf5; color: #059669; }
        .dash-kpi__icon--amber { background: #fffbeb; color: #d97706; }
        .dash-kpi__icon--sky { background: #f0f9ff; color: #0284c7; }

        .dash-kpi__label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dash-muted);
        }

        .dash-kpi__value {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .dash-kpi__hint {
            font-size: 0.8125rem;
            color: var(--dash-muted);
        }

        .dash-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .dash-badge--rose { background: #fff1f2; color: #be123c; }

        .dash-dl {
            display: grid;
            grid-template-columns: minmax(140px, 38%) 1fr;
            gap: 0.75rem 1rem;
            margin: 0;
            font-size: 0.875rem;
        }

        .dash-dl dt {
            margin: 0;
            font-weight: 600;
            color: var(--dash-muted);
        }

        .dash-dl dd {
            margin: 0;
            word-break: break-word;
        }

        .dash-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.625rem;
        }

        .dash-form {
            display: grid;
            gap: 1rem;
        }

        .dash-field label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--dash-muted);
        }

        .dash-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 10px;
            border: 1px solid var(--dash-border);
            font: inherit;
            font-size: 0.875rem;
            background: var(--dash-surface);
            color: var(--dash-text);
        }

        .dash-select:focus {
            outline: none;
            border-color: #a5b4fc;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        .dash-form__hint {
            margin: 0 0 0.5rem;
            font-size: 0.8125rem;
            color: var(--dash-muted);
        }

        .dash-btn--danger { background: #e11d48; color: #fff; }

        @media (max-width: 960px) {
            .dash-grid--2 { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .dash-dl { grid-template-columns: 1fr; gap: 0.25rem; }
            .dash-dl dt { font-size: 0.75rem; }
        }
    </style>
@endsection

@section('content')
    @php
        $currency = $user->wallet?->currency ?? 'USD';
        $currencySymbol = getCurrencyCode($currency);
        $planName = $level ?? 'Basic';
        $subscriptionActive = $subscription
            && $subscription->status === 'active'
            && $subscription->next_payment_date
            && $subscription->next_payment_date->isFuture();
        $statusClass = match ($user->status) {
            'ACTIVE' => 'dash-badge--emerald',
            'SHADOW_BANNED' => 'dash-badge--amber',
            'BLOCKED' => 'dash-badge--rose',
            default => 'dash-badge--gray',
        };
    @endphp

    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>{{ $user->name }}</h1>
                    <p>{{ '@' . $user->username }} · {{ $user->email }}</p>
                    <div class="dash-meta">
                        <span class="dash-badge dash-badge--indigo">{{ $planName }}</span>
                        @if ($subscription && ! $subscriptionActive)
                            <span class="dash-badge dash-badge--amber">Subscription expired</span>
                        @elseif ($subscriptionActive)
                            <span class="dash-badge dash-badge--emerald">Subscription active</span>
                        @endif
                        <span class="dash-badge {{ $statusClass }}">{{ str_replace('_', ' ', $user->status) }}</span>
                        @if ($user->email_verified_at)
                            <span class="dash-badge dash-badge--emerald">Verified</span>
                        @else
                            <span class="dash-badge dash-badge--gray">Unverified</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> All users
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <section class="dash-section">
                <div class="dash-grid dash-grid--4">
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Main balance</span>
                            <span class="dash-kpi__icon dash-kpi__icon--indigo"><i class="fa fa-wallet"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($user->wallet?->balance ?? 0, 2) }}</div>
                        <div class="dash-kpi__hint">Signup bonus & content earnings</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Referral balance</span>
                            <span class="dash-kpi__icon dash-kpi__icon--emerald"><i class="fa fa-users"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($user->wallet?->referral_balance ?? 0, 2) }}</div>
                        <div class="dash-kpi__hint">Friend referral earnings</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Promotion balance</span>
                            <span class="dash-kpi__icon dash-kpi__icon--amber"><i class="fa fa-bullhorn"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($user->wallet?->promoter_balance ?? 0, 2) }}</div>
                        <div class="dash-kpi__hint">Platform promotion earnings</div>
                    </div>
                    <div class="dash-kpi">
                        <div class="dash-kpi__top">
                            <span class="dash-kpi__label">Total withdrawn</span>
                            <span class="dash-kpi__icon dash-kpi__icon--sky"><i class="fa fa-money-bill-transfer"></i></span>
                        </div>
                        <div class="dash-kpi__value">{{ $currencySymbol }}{{ number_format($totalWithdrawals ?? 0, 2) }}</div>
                        <div class="dash-kpi__hint">Paid withdrawal requests</div>
                    </div>
                </div>
            </section>

            <section class="dash-section dash-grid dash-grid--2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Account details</h2>
                        <span class="dash-pill">{{ number_format($postsCount) }} posts</span>
                    </div>
                    <div class="dash-card__body">
                        <dl class="dash-dl">
                            <dt>Name</dt>
                            <dd>{{ $user->name }}</dd>
                            <dt>Username</dt>
                            <dd>{{ $user->username }}</dd>
                            <dt>Email</dt>
                            <dd>{{ $user->email }}</dd>
                            <dt>Entry channel</dt>
                            <dd>{{ $user->heard ?: '—' }}</dd>
                            <dt>Plan</dt>
                            <dd>{{ $planName }}</dd>
                            <dt>Subscription</dt>
                            <dd>
                                @if ($subscription)
                                    {{ ucfirst($subscription->status) }}
                                    @if ($subscription->next_payment_date)
                                        · {{ $subscriptionActive ? 'Renews' : 'Expired' }}
                                        {{ $subscription->next_payment_date->format('M j, Y') }}
                                    @endif
                                @else
                                    Basic (no paid plan on file)
                                @endif
                            </dd>
                            <dt>Base currency</dt>
                            <dd>{{ $currency }}</dd>
                            <dt>Access code</dt>
                            <dd>{{ $access?->code ?? '—' }}</dd>
                            <dt>Verified</dt>
                            <dd>{{ $user->email_verified_at ? $user->email_verified_at->format('M j, Y g:i A') : 'Not yet' }}</dd>
                            <dt>Joined</dt>
                            <dd>{{ $user->created_at?->format('M j, Y g:i A') }} ({{ $user->created_at?->diffForHumans() }})</dd>
                            <dt>Last updated</dt>
                            <dd>{{ $user->updated_at?->format('M j, Y g:i A') }}</dd>
                            <dt>Status</dt>
                            <dd>{{ str_replace('_', ' ', $user->status) }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Withdrawal method</h2>
                    </div>
                    <div class="dash-card__body">
                        @if ($withdrawalMethod)
                            @if ($withdrawalMethod->payment_method === 'usdt')
                                <dl class="dash-dl">
                                    <dt>Method</dt>
                                    <dd>USDT</dd>
                                    <dt>Wallet address</dt>
                                    <dd>{{ maskCode($withdrawalMethod->usdt_wallet) }}</dd>
                                </dl>
                            @elseif ($withdrawalMethod->payment_method === 'paypal')
                                <dl class="dash-dl">
                                    <dt>Method</dt>
                                    <dd>PayPal</dd>
                                    <dt>Email</dt>
                                    <dd>{{ maskCode($withdrawalMethod->paypal_email) }}</dd>
                                </dl>
                            @else
                                <dl class="dash-dl">
                                    <dt>Account name</dt>
                                    <dd>{{ $withdrawalMethod->account_name }}</dd>
                                    <dt>Bank</dt>
                                    <dd>{{ $withdrawalMethod->bank_name }}</dd>
                                    <dt>Account number</dt>
                                    <dd>{{ $withdrawalMethod->account_number }}</dd>
                                </dl>
                            @endif
                        @else
                            <div class="dash-empty">Withdrawal method not set yet.</div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="dash-section dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Quick links</h2>
                </div>
                <div class="dash-card__body">
                    <div class="dash-actions">
                        <a href="{{ route('admin.users.transactions', $user) }}" class="dash-btn dash-btn--ghost">
                            <i class="fa fa-receipt"></i> Transactions
                        </a>
                        <a href="{{ route('admin.users.posts', $user) }}" class="dash-btn dash-btn--ghost">
                            <i class="fa fa-image"></i> Posts
                        </a>
                        @if (in_array($planName, ['Creator', 'Influencer'], true))
                            <a href="{{ route('admin.users.engagement', $user) }}" class="dash-btn dash-btn--ghost">
                                <i class="fa fa-chart-line"></i> Engagement
                            </a>
                            @if ($userLevel && isAdmin())
                                <form method="POST" action="{{ route('admin.users.bonus', [$user, $planName]) }}" class="d-inline"
                                    onsubmit="return confirm('Credit upgrade bonus for {{ $planName }}?');">
                                    @csrf
                                    <button type="submit" class="dash-btn dash-btn--primary">
                                        <i class="fa fa-gift"></i>
                                        Credit bonus ({{ $currencySymbol }}{{ number_format(convertToBaseCurrency($userLevel->reg_bonus, $currency), 2) }})
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </section>

            <section class="dash-section dash-grid dash-grid--2">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Change currency</h2>
                    </div>
                    <div class="dash-card__body">
                        <p class="dash-form__hint">Updating currency clears the user's saved withdrawal method.</p>
                        <form method="POST" action="{{ route('admin.users.currency.update') }}" class="dash-form">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="dash-field">
                                <label for="currency">Currency</label>
                                <select id="currency" name="currency" class="dash-select" required>
                                    <option value="">Select currency</option>
                                    @foreach (countryList() as $country)
                                        <option value="{{ $country['code'] }}" @selected($currency === $country['code'])>
                                            {{ $country['code'] }} – {{ $country['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="dash-btn dash-btn--primary">Update currency</button>
                        </form>
                    </div>
                </div>

                @if (isAdmin())
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Upgrade plan</h2>
                    </div>
                    <div class="dash-card__body">
                        <form method="POST" action="{{ route('admin.users.upgrade') }}" class="dash-form">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="dash-field">
                                <label for="level">Target level</label>
                                <select id="level" name="level" class="dash-select" required>
                                    <option value="">Select level</option>
                                    @foreach ($levels as $planOption)
                                        <option value="{{ $planOption->id }}" @selected($planOption->name === $planName)>
                                            {{ $planOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="dash-field">
                                <label for="upgrade-code">Validation code</label>
                                <input type="text" id="upgrade-code" name="validationCode" class="dash-input"
                                    placeholder="Enter validation code" required>
                            </div>
                            <button type="submit" class="dash-btn dash-btn--primary">Upgrade user</button>
                        </form>
                    </div>
                </div>
                @endif

                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Account status</h2>
                    </div>
                    <div class="dash-card__body">
                        <form method="POST" action="{{ route('admin.users.status.update') }}" class="dash-form">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="dash-field">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="dash-select" required>
                                    <option value="">Select status</option>
                                    <option value="ACTIVE" @selected($user->status === 'ACTIVE')>Active</option>
                                    <option value="SHADOW_BANNED" @selected($user->status === 'SHADOW_BANNED')>Shadow ban</option>
                                    <option value="BLOCKED" @selected($user->status === 'BLOCKED')>Block</option>
                                </select>
                            </div>
                            <button type="submit" class="dash-btn dash-btn--danger">Update status</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
