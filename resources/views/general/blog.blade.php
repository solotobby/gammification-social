@extends('general.master.body')

@section('content')
    <section class="pagehero">
        <div class="wrap pagehero__inner">
            <div class="crumbs"><a href="{{ url('/') }}">Home</a> / <span>Blog</span></div>
            <span class="eyebrow">Payhankey blog</span>
            <h1>Tips, guides and creator stories</h1>
            <p>Everything you need to earn more, grow faster and get the most out of Payhankey — written for creators, by
                creators.</p>
        </div>
    </section>
    {{-- <section class="section">
        <div class="wrap">
            <div class="blog-feature reveal">
                <div class="blog-feature__img"><span class="post-card__cat" style="top:20px;left:20px">Featured</span></div>
                <div class="blog-feature__body">
                    <div class="blog-cats" style="margin-bottom:14px"><span class="chip is-active"
                            style="cursor:default">Earning</span></div>
                    <h2 style="font-size:1.9rem">How to maximize your earnings on Payhankey</h2>
                    <p class="lead" style="margin:14px 0 18px">The creators earning the most aren't posting more — they're
                        posting smarter. We broke down seven habits that consistently turn engagement into a reliable
                        monthly payout.</p>
                    <div class="post-meta" style="margin-bottom:22px">
                        <div class="avatar" style="background:var(--grad-brand)">AO</div><span>Amara
                            O.</span><span>·</span><span>Jun 12, 2026</span><span>·</span><span>6 min read</span>
                    </div>
                    <a class="btn btn--primary" href="#">Read article <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg></a>
                </div>
            </div>
        </div>
    </section> --}}

    <section class="section--tight">
        <div class="wrap">
            <div
                style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;margin-bottom:30px">
                <div class="blog-cats">
                    <span class="chip is-active" data-cat="all">All</span>
                    @foreach ($blogCategories as $category)
                        <span class="chip" data-cat="{{ $category->id }}">{{ $category->name }}</span>
                    @endforeach
                </div>
                <div class="searchbar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg><input class="input" type="search" placeholder="Search articles" aria-label="Search articles">
                </div>
            </div>
            <div class="grid-3">
                @foreach ($blogs as $blog)
                    <article class="post-card reveal" data-post-cat="{{ $blog->blogCategory->id }}">
                        <a href={{ url('blog/' . $blog->slug) }}>
                            <div class="post-card__img">
                                <span class="post-card__cat">{{ $blog->blogCategory->name }}</span>
                            </div>
                            <div class="post-card__body">
                                <h3>{{ $blog->title }}</h3>
                                <p>{{ $blog->excerpt }}</p>
                                <div class="post-meta">
                                    <div class="avatar" style="background:var(--grad-brand)">AS</div>
                                    <span>Anthony
                                        S.</span><span>·</span><span>{{ $blog->created_at->format('M d') }}</span><span>·</span><span>{{ ceil(str_word_count($blog->content) / 200) ?: 0 }}
                                        min read</span>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
                {{-- <article class="post-card reveal" data-post-cat="earning">
                    <div class="post-card__img "><span class="post-card__cat">Earning</span></div>
                    <div class="post-card__body">
                        <h3>How to maximize your earnings on Payhankey</h3>
                        <p>Seven practical habits that help top creators turn ordinary posts into a steady monthly income.
                        </p>
                        <div class="post-meta">
                            <div class="avatar" style="background:var(--grad-brand)">AO</div><span>Amara
                                O.</span><span>·</span><span>Jun 12</span><span>·</span><span>6 min read</span>
                        </div>
                    </div>
                </article>
                <article class="post-card reveal" data-post-cat="guides">
                    <div class="post-card__img alt2"><span class="post-card__cat">Guides</span></div>
                    <div class="post-card__body">
                        <h3>The complete beginner's guide to getting started</h3>
                        <p>New to Payhankey? This step-by-step walkthrough takes you from signup to your first withdrawal.
                        </p>
                        <div class="post-meta">
                            <div class="avatar" style="background:linear-gradient(135deg,#12B886,#5A4FDC)">TE</div>
                            <span>Team Payhankey</span><span>·</span><span>Jun 9</span><span>·</span><span>8 min read</span>
                        </div>
                    </div>
                </article>
                <article class="post-card reveal" data-post-cat="growth">
                    <div class="post-card__img alt3"><span class="post-card__cat">Growth</span></div>
                    <div class="post-card__body">
                        <h3>10 tips for growing your online income</h3>
                        <p>From posting consistency to smart referrals, here's how to build income that compounds.</p>
                        <div class="post-meta">
                            <div class="avatar" style="background:linear-gradient(135deg,#F5B73C,#F25C8A)">KB</div>
                            <span>Kwame B.</span><span>·</span><span>Jun 5</span><span>·</span><span>5 min read</span>
                        </div>
                    </div>
                </article>
                <article class="post-card reveal" data-post-cat="payouts">
                    <div class="post-card__img alt4"><span class="post-card__cat">Payouts</span></div>
                    <div class="post-card__body">
                        <h3>PayPal, USDT or bank: choosing your payout method</h3>
                        <p>A clear comparison of every withdrawal option so you can pick the one that suits you best.</p>
                        <div class="post-meta">
                            <div class="avatar" style="background:linear-gradient(135deg,#7C6FF2,#F25C8A)">TE</div>
                            <span>Team Payhankey</span><span>·</span><span>Jun 1</span><span>·</span><span>4 min read</span>
                        </div>
                    </div>
                </article>
                <article class="post-card reveal" data-post-cat="creators">
                    <div class="post-card__img alt5"><span class="post-card__cat">Creators</span></div>
                    <div class="post-card__body">
                        <h3>Creator vs Influencer: which level is right for you?</h3>
                        <p>Understand the differences in earnings, badges and reach — and when it pays to upgrade.</p>
                        <div class="post-meta">
                            <div class="avatar" style="background:linear-gradient(135deg,#16122E,#5A4FDC)">ZM</div>
                            <span>Zinhle M.</span><span>·</span><span>May 28</span><span>·</span><span>6 min read</span>
                        </div>
                    </div>
                </article>
                <article class="post-card reveal" data-post-cat="growth">
                    <div class="post-card__img alt2"><span class="post-card__cat">Growth</span></div>
                    <div class="post-card__body">
                        <h3>Going viral: earning from promotional videos</h3>
                        <p>Make a video about Payhankey, tag us, and earn up to $10 per 1,000 views. Here's how.</p>
                        <div class="post-meta">
                            <div class="avatar" style="background:linear-gradient(135deg,#12B886,#5A4FDC)">CN</div>
                            <span>Chidi N.</span><span>·</span><span>May 24</span><span>·</span><span>5 min read</span>
                        </div>
                    </div>
                </article> --}}
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            <div class="cta-band reveal">
                <h2>Don't just read about earning — start.</h2>
                <p>Put these tips into practice on a free Payhankey account and watch your first earnings roll in.</p>
                <div class="hero__cta"><a class="btn btn--white btn--lg" href="{{ url('/register') }}">Create free account
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg></a></div>
            </div>
        </div>
    </section>
@endsection
