<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Grow together</span>
            <h1>My referrals</h1>
            <p>Invite friends, track signups, and unlock monetization perks when you hit referral milestones.</p>
        </div>
    </div>

    @if ($qualifiedForMonetization)
        <div class="pk-alert pk-alert--success">
            <strong>Congratulations!</strong> You invited {{ number_format($monthlyReferralsCount) }} friends this month and qualified for free content monetization.
        </div>
    @else
        <div class="pk-alert pk-alert--info">
            Invite <strong>500 friends this month</strong> to unlock free monetization access. You currently have
            <strong>{{ number_format($monthlyReferralsCount) }}</strong> this month.
            Earn rewards when referrals upgrade — including bonuses for influencer referrals.
        </div>
    @endif

    <div class="pk-copy-bar">
        <code id="referralLink">{{ $referralLink }}</code>
        <button type="button" class="pk-btn pk-btn--primary" onclick="copyReferralLink(this)">
            <i class="fa fa-copy"></i> Copy link
        </button>
    </div>

    <div class="pk-stat-grid">
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:var(--pk-violet-soft);color:var(--pk-violet);"><i class="fa fa-users"></i></div>
            <p class="pk-stat-card-value">{{ number_format($totalReferrals) }}</p>
            <p class="pk-stat-card-label">Total referrals</p>
        </article>
        <article class="pk-stat-card">
            <div class="pk-stat-card-icon" style="background:var(--pk-mint-soft);color:var(--pk-mint);"><i class="fa fa-calendar"></i></div>
            <p class="pk-stat-card-value">{{ number_format($monthlyReferralsCount) }}</p>
            <p class="pk-stat-card-label">This month</p>
        </article>
    </div>

    <div class="pk-panel">
        <div class="pk-panel-head"><h2>Referred users</h2></div>
        @if ($referralList->count())
            <div class="pk-table-wrap">
                <table class="pk-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Followers</th>
                            <th>Following</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($referralList as $person)
                            <tr wire:key="ref-{{ $person->id }}">
                                <td>
                                    <a href="{{ url('profile/' . $person->username) }}" class="pk-user-row" style="text-decoration:none;color:inherit">
                                        <img src="{{ $person->avatar ?: asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                        <div>
                                            <b>{{ displayName($person->name) }}</b>
                                            <small>@{{ $person->username }}</small>
                                        </div>
                                    </a>
                                </td>
                                <td>{{ number_format($person->followers) }}</td>
                                <td>{{ number_format($person->following) }}</td>
                                <td>{{ \Carbon\Carbon::parse($person->created_at)->format('M j, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($referralList->hasPages())
                <div class="pk-pagination">
                    <div class="pk-pg-info">{{ $referralList->total() }} total</div>
                    <div class="pk-pg-btns">
                        <button type="button" class="pk-pg-btn" wire:click="previousPage" @disabled($referralList->onFirstPage())>Prev</button>
                        <button type="button" class="pk-pg-btn" wire:click="nextPage" @disabled(! $referralList->hasMorePages())>Next</button>
                    </div>
                </div>
            @endif
        @else
            <div class="pk-empty">
                <h3>No referrals yet</h3>
                <p>Share your link above — when friends join, they'll appear here.</p>
            </div>
        @endif
    </div>

    <script>
        function copyReferralLink(btn) {
            const text = document.getElementById('referralLink').innerText.trim();
            navigator.clipboard?.writeText(text).catch(() => {
                const t = document.createElement('input');
                t.value = text;
                document.body.appendChild(t);
                t.select();
                document.execCommand('copy');
                document.body.removeChild(t);
            });
            btn.innerHTML = '<i class="fa fa-check"></i> Copied';
            setTimeout(() => btn.innerHTML = '<i class="fa fa-copy"></i> Copy link', 2000);
        }
    </script>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
