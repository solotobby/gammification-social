<div class="content-side">

    @if (isAdminPanelUser())
        <ul class="nav-main">
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->routeIs('admin.home') ? ' active' : '' }}" href="{{ route('admin.home') }}">
                    <i class="nav-main-link-icon fa fa-home"></i>
                    <span class="nav-main-link-name">Dashboard</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.users.*') ? ' active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="nav-main-link-icon fa fa-users"></i>
                    <span class="nav-main-link-name">User List</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.outreach.*') ? ' active' : '' }}" href="{{ route('admin.outreach.index') }}">
                    <i class="nav-main-link-icon fa fa-bullhorn"></i>
                    <span class="nav-main-link-name">Outreach Leaders</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.communities.*') ? ' active' : '' }}" href="{{ route('admin.communities.index') }}">
                    <i class="nav-main-link-icon fa fa-object-group"></i>
                    <span class="nav-main-link-name">Communities</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.posts.*') ? ' active' : '' }}" href="{{ route('admin.posts.index') }}">
                    <i class="nav-main-link-icon fa fa-newspaper"></i>
                    <span class="nav-main-link-name">Timeline Posts</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.reports.*') ? ' active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="nav-main-link-icon fa fa-flag"></i>
                    <span class="nav-main-link-name">Post Reports</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.feedback.*') ? ' active' : '' }}" href="{{ route('admin.feedback.index') }}">
                    <i class="nav-main-link-icon fa fa-comment-dots"></i>
                    <span class="nav-main-link-name">User Feedback</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.videos.*') ? ' active' : '' }}" href="{{ route('admin.videos.index') }}">
                    <i class="nav-main-link-icon fa fa-film"></i>
                    <span class="nav-main-link-name">Rolls / Videos</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.bookmarks.*') ? ' active' : '' }}" href="{{ route('admin.bookmarks.index') }}">
                    <i class="nav-main-link-icon fa fa-bookmark"></i>
                    <span class="nav-main-link-name">Bookmarks</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.messaging.*') ? ' active' : '' }}" href="{{ route('admin.messaging.index') }}">
                    <i class="nav-main-link-icon fa fa-comments"></i>
                    <span class="nav-main-link-name">Messaging</span>
                </a>

            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                    aria-expanded="false" href="#">
                    <i class="nav-main-link-icon fa fa-blog"></i>
                    <span class="nav-main-link-name">Blog</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.blog.create') }}">
                            <span class="nav-main-link-name">Create</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.blog.index') }}">
                            <span class="nav-main-link-name">View</span>
                        </a>
                    </li>
                </ul>
            </li>

            @if (isAdmin())
                <a class="nav-main-link{{ request()->routeIs('admin.finance.*') ? ' active' : '' }}" href="{{ route('admin.finance.index') }}">
                    <i class="nav-main-link-icon fa fa-wallet"></i>
                    <span class="nav-main-link-name">Wallets & Earnings</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.paykoin.*') ? ' active' : '' }}" href="{{ route('admin.paykoin.index') }}">
                    <i class="nav-main-link-icon fa fa-gift"></i>
                    <span class="nav-main-link-name">PayKoin</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.korapay.*') ? ' active' : '' }}" href="{{ route('admin.korapay.index') }}">
                    <i class="nav-main-link-icon fa fa-building-columns"></i>
                    <span class="nav-main-link-name">Kora Pay</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.flutterwave.*') ? ' active' : '' }}" href="{{ route('admin.flutterwave.index') }}">
                    <i class="nav-main-link-icon fa fa-wave-square"></i>
                    <span class="nav-main-link-name">Flutterwave</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.audit-logs.*') ? ' active' : '' }}" href="{{ route('admin.audit-logs.index') }}">
                    <i class="nav-main-link-icon fa fa-shield-halved"></i>
                    <span class="nav-main-link-name">Audit Log</span>
                </a>
                <a class="nav-main-link{{ request()->routeIs('admin.staff.*') ? ' active' : '' }}" href="{{ route('admin.staff.index') }}">
                    <i class="nav-main-link-icon fa fa-user-tie"></i>
                    <span class="nav-main-link-name">Staff</span>
                </a>
                <a class="nav-main-link" href="{{ route('admin.currencies.index') }}">
                    <i class="nav-main-link-icon fa fa-coins"></i>
                    <span class="nav-main-link-name">Currency</span>
                </a>
                <a class="nav-main-link" href="{{ route('admin.levels.index') }}">
                    <i class="nav-main-link-icon fa fa-layer-group"></i>
                    <span class="nav-main-link-name">Level Management</span>
                </a>
                <a class="nav-main-link" href="{{ route('admin.payouts.pro-rata') }}">
                    <i class="nav-main-link-icon fa fa-chart-pie"></i>
                    <span class="nav-main-link-name">Pro-Rata</span>
                </a>
                <a class="nav-main-link" href="{{ route('admin.payouts.current') }}">
                    <i class="nav-main-link-icon fa fa-calendar-check"></i>
                    <span class="nav-main-link-name">Current Payout</span>
                </a>

            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                    aria-expanded="false" href="#">
                    <i class="nav-main-link-icon fa fa-hand-holding-dollar"></i>
                    <span class="nav-main-link-name">Payouts</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.payouts.levels.show', 'Influencer') }}">
                            <span class="nav-main-link-name">Influencers</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.payouts.levels.show', 'Creator') }}">
                            <span class="nav-main-link-name">Creators</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.payouts.levels.show', 'Basic') }}">
                            <span class="nav-main-link-name">Basic</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                    aria-expanded="false" href="#">
                    <i class="nav-main-link-icon fa fa-graduation-cap"></i>
                    <span class="nav-main-link-name">Academy</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.academy.create') }}">
                            <span class="nav-main-link-name">Create</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.academy.index') }}">
                            <span class="nav-main-link-name">View</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                    aria-expanded="false" href="#">
                    <i class="nav-main-link-icon fa fa-life-ring"></i>
                    <span class="nav-main-link-name">Help Center</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.help.create') }}">
                            <span class="nav-main-link-name">Create</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.help.index') }}">
                            <span class="nav-main-link-name">View</span>
                        </a>
                    </li>
                </ul>
            </li>

            <a class="nav-main-link" href="{{ route('admin.bank-accounts.index') }}">
                <i class="nav-main-link-icon fa fa-building-columns"></i>
                <span class="nav-main-link-name">Bank Account</span>
            </a>
            @endif

            </li>
        </ul>
    @else
        @php
            $earnOpen = request()->is('upgrade*')
                || request()->is('wallets*')
                || request()->is('transaction/list*')
                || request()->is('analytics*')
                || request()->is('referral/list*')
                || request()->is('bank/information*');

            $accountOpen = request()->is('profile/*')
                || request()->is('settings*')
                || request()->is('feedback*')
                || request()->is('how/it/works*');
        @endphp

        <ul class="nav-main">
            {{-- Create & engage --}}
            <li class="nav-main-heading">Feed</li>
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('home') || request()->is('timeline*') || request()->is('user/home') ? ' active' : '' }}" href="{{ url('home') }}">
                    <i class="nav-main-link-icon fa fa-home"></i>
                    <span class="nav-main-link-name">Dashboard</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->routeIs('rolls.*') ? ' active' : '' }}" href="{{ route('rolls.random') }}">
                    <i class="nav-main-link-icon fa fa-circle-play"></i>
                    <span class="nav-main-link-name">Rolls</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->routeIs('community*') ? ' active' : '' }}" href="{{ route('community') }}">
                    <i class="nav-main-link-icon fa fa-users"></i>
                    <span class="nav-main-link-name">Communities</span>
                </a>
            </li>
            @auth
                @if (auth()->user()->hasRole('user'))
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->routeIs('messages*') ? ' active' : '' }}" href="{{ route('messages') }}">
                        <i class="nav-main-link-icon fa fa-comment-dots"></i>
                        <span class="nav-main-link-name">Messages
                            @php $msgUnread = messagingUnreadTotal(); @endphp
                            @if ($msgUnread > 0)
                                <span class="badge bg-primary ms-1">{{ $msgUnread > 99 ? '99+' : $msgUnread }}</span>
                            @endif
                        </span>
                    </a>
                </li>
                @endif
                <li class="nav-main-item">
                    <a class="nav-main-link{{ request()->routeIs('bookmarks') ? ' active' : '' }}" href="{{ route('bookmarks') }}">
                        <i class="nav-main-link-icon fa fa-bookmark"></i>
                        <span class="nav-main-link-name">Bookmarks</span>
                    </a>
                </li>
            @endauth

            {{-- Monetization --}}
            <li class="nav-main-heading">Monetize</li>
            <li class="nav-main-item{{ $earnOpen ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu{{ $earnOpen ? ' active' : '' }}"
                    data-toggle="submenu"
                    aria-haspopup="true"
                    aria-expanded="{{ $earnOpen ? 'true' : 'false' }}"
                    href="#">
                    <i class="nav-main-link-icon fa fa-coins"></i>
                    <span class="nav-main-link-name">Earnings</span>
                </a>
                <ul class="nav-main-submenu" @if ($earnOpen) style="display:block" @endif>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('upgrade*') ? ' active' : '' }}" href="{{ url('upgrade') }}">
                            <span class="nav-main-link-name">Upgrade</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('wallets*') ? ' active' : '' }}" href="{{ url('wallets') }}">
                            <span class="nav-main-link-name">Wallet</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('transaction/list*') ? ' active' : '' }}" href="{{ url('transaction/list') }}">
                            <span class="nav-main-link-name">Transactions</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('analytics*') ? ' active' : '' }}" href="{{ url('analytics') }}">
                            <span class="nav-main-link-name">Analytics</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('referral/list*') ? ' active' : '' }}" href="{{ url('referral/list') }}">
                            <span class="nav-main-link-name">My Referrals</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('bank/information*') ? ' active' : '' }}" href="{{ url('bank/information') }}">
                            <span class="nav-main-link-name">Bank Information</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Account & help --}}
            <li class="nav-main-heading">Account</li>
            <li class="nav-main-item{{ $accountOpen ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu{{ $accountOpen ? ' active' : '' }}"
                    data-toggle="submenu"
                    aria-haspopup="true"
                    aria-expanded="{{ $accountOpen ? 'true' : 'false' }}"
                    href="#">
                    <i class="nav-main-link-icon si si-settings"></i>
                    <span class="nav-main-link-name">Settings</span>
                </a>
                <ul class="nav-main-submenu" @if ($accountOpen) style="display:block" @endif>
                    @auth
                        <li class="nav-main-item">
                            <a class="nav-main-link{{ request()->is('profile/' . auth()->user()->username) || request()->is('profile/' . auth()->user()->username . '/*') ? ' active' : '' }}"
                                href="{{ url('profile/' . auth()->user()->username) }}">
                                <span class="nav-main-link-name">Profile</span>
                            </a>
                        </li>
                    @endauth
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('settings*') ? ' active' : '' }}" href="{{ url('settings') }}">
                            <span class="nav-main-link-name">Account</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->routeIs('feedback') ? ' active' : '' }}" href="{{ route('feedback') }}">
                            <span class="nav-main-link-name">Send Feedback</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('how/it/works*') ? ' active' : '' }}" href="{{ url('how/it/works') }}">
                            <span class="nav-main-link-name">How It Works</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('user/blog*') || request()->is('blog*') ? ' active' : '' }}" href="{{ url('user/blog') }}">
                    <i class="nav-main-link-icon fa fa-blog"></i>
                    <span class="nav-main-link-name">Blog</span>
                </a>
            </li>
        </ul>
    @endif
</div>
