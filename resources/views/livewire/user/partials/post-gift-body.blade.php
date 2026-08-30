{{-- Modal, strip, toast — requires parent .pk-gift-root with postGiftPanel x-data --}}

    <div class="pk-gifts-strip" x-show="($store.postGifts.posts[postId]?.total ?? 0) > 0" x-cloak>
        <span class="pk-gifts-strip-label">
            <i class="fa fa-gift"></i>
            <span x-text="($store.postGifts.posts[postId]?.total ?? 0) + (($store.postGifts.posts[postId]?.total ?? 0) === 1 ? ' gift' : ' gifts')"></span>
        </span>
        <template x-for="g in ($store.postGifts.posts[postId]?.recent ?? []).slice(0, 4)" :key="g.id">
            <span class="pk-gift-chip">
                <span class="pk-gift-chip-emoji" x-text="g.emoji"></span>
                <span x-text="'@' + g.sender"></span>
            </span>
        </template>
        <span class="pk-gift-chip" x-show="($store.postGifts.posts[postId]?.recent ?? []).length > 4" x-text="'+' + (($store.postGifts.posts[postId]?.recent ?? []).length - 4) + ' more'"></span>
    </div>

    <div class="pk-gift-burst-layer" aria-hidden="true">
        <template x-for="particle in burstParticles" :key="particle.id">
            <span
                class="pk-gift-particle"
                :class="'pk-gift-particle--' + particle.kind"
                :style="particle.style"
                x-text="particle.emoji"
            ></span>
        </template>
    </div>

    <div class="pk-gift-spend-flash" :class="{ 'is-active': spendFlash }" aria-hidden="true"></div>

    <span
        class="pk-gift-float"
        x-show="floatEmoji"
        x-text="floatEmoji"
        :key="floatKey"
        aria-hidden="true"
    ></span>

    <div
        class="pk-gift-overlay"
        x-show="open"
        x-transition.opacity
        @keydown.escape.window="close()"
        @click.self="close()"
        x-cloak
    >
        <div class="pk-gift-sheet" role="dialog" aria-modal="true" @click.stop>
            <header class="pk-gift-sheet-head">
                <div style="flex:1;min-width:0">
                    <p class="pk-gift-sheet-kicker">Send a gift</p>
                    <h3 x-text="'Support ' + creator"></h3>
                    <small x-text="'Gift PayKoin artifacts on this post · @' + username"></small>
                </div>
                <div class="pk-gift-balance" :class="{ 'pk-gift-balance--spent': balancePulse }">
                    <div>
                        <strong x-text="fmtPk($store.postGifts.spendable) + ' PK'"></strong>
                        <a href="{{ url('wallets') }}" wire:navigate>Top up</a>
                    </div>
                </div>
                <button type="button" class="pk-gift-close" @click="close()" aria-label="Close">&times;</button>
            </header>

            <div class="pk-gift-low-balance" x-show="$store.postGifts.spendable < minPrice" x-cloak>
                <span>Not enough PayKoin to send gifts.</span>
                <a href="{{ url('wallets') }}" wire:navigate>Top up →</a>
            </div>

            <div class="pk-gift-grid-wrap">
                <div class="pk-gift-grid">
                    <template x-for="item in allArtifacts()" :key="item.id">
                        <button
                            type="button"
                            class="pk-gift-artifact"
                            :class="[
                                'pk-gift-artifact--' + item.tier,
                                { 'pk-gift-artifact--sending': sendingId === item.id },
                                { 'pk-gift-artifact--sent': sentId === item.id },
                            ]"
                            :disabled="$store.postGifts.spendable < item.price || sending"
                            @click="sendGift(item, $event)"
                            :title="'Send ' + item.name + ' (' + item.price + ' PK)'"
                        >
                            <span class="pk-gift-artifact-icon" x-text="item.emoji"></span>
                            <span class="pk-gift-artifact-name" x-text="item.name"></span>
                            <span class="pk-gift-artifact-price" x-text="item.price + ' PK'"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div class="pk-gift-toast" :class="{ 'pk-gift-toast--celebrate': toastCelebrate }" x-show="toast" x-transition x-cloak>
        <span class="pk-gift-toast-emoji" x-text="toastEmoji"></span>
        <span x-text="toast"></span>
    </div>

@once
<script>
    window.postGiftCatalog = @json(config('payhankey.paykoin.gift_artifacts', []));

    window.initPostGiftStore = window.initPostGiftStore || function () {
        if (Alpine.store('postGifts')) {
            return;
        }

        Alpine.store('postGifts', {
            posts: {},
            spendable: 0,
            openPostId: null,

            ensurePost(postId, initialGifts, spendable) {
                this.syncPost(postId, {
                    total: initialGifts?.total ?? 0,
                    recent: initialGifts?.recent ?? [],
                    spendable,
                });
            },

            applyGift(postId, gift, spendable, giftTotal) {
                const current = this.posts[postId] ?? { total: 0, recent: [] };
                const recent = [gift, ...current.recent.filter(g => g.id !== gift.id)].slice(0, 20);

                this.posts[postId] = {
                    total: giftTotal ?? (current.total + 1),
                    recent,
                };

                if (typeof spendable === 'number') {
                    this.spendable = spendable;
                }
            },

            revertGift(postId, giftId, price) {
                const current = this.posts[postId];
                if (!current) return;

                this.posts[postId] = {
                    total: Math.max(0, current.total - 1),
                    recent: current.recent.filter(g => g.id !== giftId),
                };

                this.spendable += price;
            },

            syncPost(postId, data) {
                if (!data) return;

                this.posts[postId] = {
                    total: data.total ?? 0,
                    recent: [...(data.recent ?? [])],
                };

                if (typeof data.spendable === 'number') {
                    this.spendable = data.spendable;
                }
            },
        });
    };

    document.addEventListener('alpine:init', window.initPostGiftStore);
    if (window.Alpine) {
        window.initPostGiftStore();
    }

    window.postGiftPanel = window.postGiftPanel || function (config) {
        return {
            postId: config.postId,
            creator: config.creator,
            username: config.username,
            sender: config.sender || 'you',
            giftableType: config.giftableType || 'community_post',
            canSend: config.canSend !== false,

            open: false,
            toast: '',
            toastEmoji: '',
            toastCelebrate: false,
            floatEmoji: '',
            floatKey: 0,
            sending: false,
            sendingId: null,
            sentId: null,
            spendFlash: false,
            balancePulse: false,
            burstParticles: [],

            catalog: window.postGiftCatalog,

            get store() {
                return Alpine.store('postGifts');
            },

            get recentGifts() {
                return this.store.posts[this.postId]?.recent ?? [];
            },

            get giftTotal() {
                return this.store.posts[this.postId]?.total ?? 0;
            },

            get spendable() {
                return this.store.spendable;
            },

            get minPrice() {
                if (!this.catalog.length) return 999999;
                return Math.min(...this.catalog.map(a => a.price));
            },

            init() {
                if (window.initPostGiftStore) {
                    window.initPostGiftStore();
                }

                this.store.syncPost(this.postId, {
                    total: config.initialGifts?.total ?? 0,
                    recent: config.initialGifts?.recent ?? [],
                    spendable: config.spendable ?? 0,
                });

                this.$nextTick(() => this.refreshFromServer());

                this._onRefreshAll = () => this.refreshFromServer();
                window.addEventListener('pk-gifts-refresh-all', this._onRefreshAll);
            },

            fmtPk(n) {
                return new Intl.NumberFormat().format(Math.round(n || 0));
            },

            allArtifacts() {
                return [...this.catalog].sort((a, b) => a.price - b.price);
            },

            async refreshFromServer() {
                if (!this.$wire?.loadPostGifts) return;

                try {
                    const data = await this.$wire.loadPostGifts(this.postId, this.giftableType);
                    this.store.syncPost(this.postId, data);
                } catch (_) {
                    /* keep store state */
                }
            },

            show() {
                this.store.openPostId = this.postId;
                this.open = true;
                this.refreshFromServer();
            },

            close() {
                if (this.store.openPostId === this.postId) {
                    this.store.openPostId = null;
                }
                this.open = false;
            },

            playSpendEffects(item, event) {
                this.floatEmoji = item.emoji;
                this.floatKey = Date.now();

                this.spendFlash = true;
                setTimeout(() => { this.spendFlash = false; }, 700);

                this.balancePulse = true;
                setTimeout(() => { this.balancePulse = false; }, 950);

                this.sentId = item.id;
                setTimeout(() => { this.sentId = null; }, 1300);

                const rect = event?.currentTarget?.getBoundingClientRect?.();
                const originX = rect ? rect.left + rect.width / 2 : window.innerWidth / 2;
                const originY = rect ? rect.top + rect.height / 2 : window.innerHeight / 2;

                const particles = [];
                const emojis = [item.emoji, '✨', '💫', '⭐', '🎉', '💜', '🪙'];
                for (let i = 0; i < 12; i++) {
                    const angle = (Math.PI * 2 * i) / 12;
                    const distance = 50 + Math.random() * 90;
                    particles.push({
                        id: Date.now() + '-' + i,
                        emoji: emojis[i % emojis.length],
                        kind: i === 0 ? 'hero' : 'spark',
                        style: `--tx:${Math.cos(angle) * distance}px;--ty:${Math.sin(angle) * distance - 40}px;left:${originX}px;top:${originY}px;animation-delay:${i * 30}ms;`,
                    });
                }

                this.burstParticles = particles;
                setTimeout(() => { this.burstParticles = []; }, 1500);
                setTimeout(() => { this.floatEmoji = ''; }, 2400);
            },

            showToast(emoji, message, celebrate = false) {
                this.toastCelebrate = celebrate;
                this.toastEmoji = emoji;
                this.toast = message;
                setTimeout(() => {
                    this.toast = '';
                    this.toastCelebrate = false;
                }, celebrate ? 3200 : 3000);
            },

            async sendGift(item, event) {
                if (this.sending || this.$store.postGifts.spendable < item.price) return;
                if (!this.$wire?.sendPostGift) return;

                const pendingGift = {
                    id: 'pending-' + Date.now(),
                    emoji: item.emoji,
                    name: item.name,
                    price: item.price,
                    sender: this.sender,
                };

                const previousSpendable = this.$store.postGifts.spendable;
                const previousTotal = this.$store.postGifts.posts[this.postId]?.total ?? 0;

                this.sending = true;
                this.sendingId = item.id;

                this.store.applyGift(
                    this.postId,
                    pendingGift,
                    previousSpendable - item.price,
                    previousTotal + 1,
                );

                this.playSpendEffects(item, event);
                this.showToast(item.emoji, item.name + ' sent to ' + this.creator + '!', true);

                try {
                    const result = await this.$wire.sendPostGift(this.postId, item.id, this.giftableType);

                    if (!result?.ok) {
                        this.store.revertGift(this.postId, pendingGift.id, item.price);
                        this.showToast('⚠️', result?.message || 'Unable to send gift.');
                        return;
                    }

                    if (result.gift) {
                        this.store.applyGift(
                            this.postId,
                            result.gift,
                            result.spendable ?? this.spendable,
                            result.giftTotal ?? this.giftTotal,
                        );
                    }

                    if (this.$store.postGifts.spendable < this.minPrice) {
                        setTimeout(() => this.close(), 900);
                    }
                } catch (_) {
                    this.store.revertGift(this.postId, pendingGift.id, item.price);
                    this.showToast('⚠️', 'Unable to send gift. Try again.');
                } finally {
                    this.sending = false;
                    this.sendingId = null;
                }
            },
        };
    };
</script>
@endonce
