<div>
    {{-- Because she competes with no one, no one can compete with her. --}}

    <div class="communities-page">

        @verbatim
            <style>
                .communities-page {
                    --pk-violet: #5A4FDC;
                    --pk-violet-dark: #4B41C4;
                    --pk-violet-tint: #EEECFC;
                    --pk-mint: #1FAE64;
                    --pk-mint-tint: #E6F7EE;
                    --pk-mint-line: #CBEBDA;
                    --pk-gold: #E3A421;
                    --pk-red: #EF4444;
                    --pk-ink: #171B24;
                    --pk-gray-700: #4B5163;
                    --pk-gray-500: #8A8FA3;
                    --pk-gray-400: #AEB2C2;
                    --pk-line: #E7E8F0;
                    --pk-line-strong: #DADCE9;
                    --pk-r-sm: 8px;
                    --pk-r-md: 12px;
                    --pk-r-lg: 14px;
                    --pk-r-pill: 999px;
                    --pk-shadow: 0 1px 2px rgba(23, 27, 36, .04);
                    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
                    color: var(--pk-ink);
                }

                .communities-page * {
                    box-sizing: border-box
                }

                .communities-page .pk-card {
                    background: #fff;
                    border: 1px solid var(--pk-line);
                    border-radius: var(--pk-r-lg);
                    box-shadow: var(--pk-shadow)
                }

                .communities-page .pk-banner {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    padding: 18px 20px;
                    margin-bottom: 16px;
                    flex-wrap: wrap;
                    background: var(--pk-violet-tint);
                    border-color: #DFDAF9
                }

                .communities-page .pk-banner-ic {
                    width: 44px;
                    height: 44px;
                    border-radius: var(--pk-r-md);
                    background: var(--pk-violet);
                    display: grid;
                    place-items: center;
                    flex: none
                }

                .communities-page .pk-banner-ic svg {
                    width: 20px;
                    height: 20px;
                    color: #fff
                }

                .communities-page .pk-banner-copy {
                    flex: 1;
                    min-width: 220px
                }

                .communities-page .pk-banner-copy h2 {
                    font-size: 1rem;
                    font-weight: 800;
                    margin: 0 0 3px
                }

                .communities-page .pk-banner-copy p {
                    font-size: .84rem;
                    color: var(--pk-gray-700);
                    margin: 0
                }

                .communities-page .pk-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 10px 18px;
                    border-radius: var(--pk-r-md);
                    font-weight: 700;
                    font-size: .86rem;
                    transition: .15s;
                    flex: none;
                    white-space: nowrap;
                    border: none;
                    cursor: pointer;
                    font-family: inherit
                }

                .communities-page .pk-btn svg {
                    width: 15px;
                    height: 15px
                }

                .communities-page .pk-btn-violet {
                    background: var(--pk-violet);
                    color: #fff
                }

                .communities-page .pk-btn-violet:hover {
                    background: var(--pk-violet-dark)
                }

                .communities-page .pk-btn-outline {
                    background: #fff;
                    border: 1.3px solid var(--pk-line-strong);
                    color: var(--pk-gray-700)
                }

                .communities-page .pk-btn-outline:hover {
                    border-color: var(--pk-violet);
                    color: var(--pk-violet)
                }

                .communities-page .pk-btn[disabled] {
                    opacity: .5;
                    pointer-events: none
                }

                .communities-page .pk-btn-sm {
                    padding: 7px 14px;
                    font-size: .78rem
                }

                .communities-page .pk-sec-head {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 12px
                }

                .communities-page .pk-sec-head h3 {
                    font-size: 1rem;
                    font-weight: 800;
                    margin: 0
                }

                .communities-page .pk-search-row {
                    display: flex;
                    align-items: center;
                    gap: 9px;
                    background: #F4F5F9;
                    border-radius: var(--pk-r-pill);
                    padding: 9px 16px;
                    margin-bottom: 14px;
                    color: var(--pk-gray-500)
                }

                .communities-page .pk-search-row svg {
                    width: 16px;
                    height: 16px;
                    flex: none
                }

                .communities-page .pk-search-row input {
                    border: none;
                    outline: none;
                    font-family: inherit;
                    font-size: .86rem;
                    width: 100%;
                    background: none;
                    color: var(--pk-ink)
                }

                .communities-page .pk-f-chip {
                    flex: none;
                    font-size: .8rem;
                    font-weight: 700;
                    padding: 8px 14px;
                    border-radius: var(--pk-r-pill);
                    background: #fff;
                    border: 1px solid var(--pk-line);
                    color: var(--pk-gray-700);
                    transition: .15s;
                    cursor: pointer;
                    font-family: inherit
                }

                .communities-page .pk-f-chip:hover {
                    border-color: var(--pk-violet)
                }

                .communities-page .pk-f-chip.pk-sel {
                    background: var(--pk-violet);
                    border-color: var(--pk-violet);
                    color: #fff
                }

                .communities-page .pk-comm-list {
                    display: flex;
                    flex-direction: column;
                    gap: 14px
                }

                .communities-page .pk-empty {
                    text-align: center;
                    padding: 50px 20px;
                    color: var(--pk-gray-500);
                    background: #fff;
                    border: 1px dashed var(--pk-line-strong);
                    border-radius: var(--pk-r-lg)
                }

                .communities-page .pk-empty svg {
                    width: 38px;
                    height: 38px;
                    color: var(--pk-line-strong);
                    margin: 0 auto 12px
                }

                .communities-page .pk-empty b {
                    display: block;
                    color: var(--pk-ink);
                    font-size: .94rem;
                    margin-bottom: 3px
                }

                .communities-page .pk-load-more-row {
                    margin-top: 18px
                }

                .communities-page .pk-comm-card {
                    padding: 16px 18px
                }

                .communities-page .pk-cc-top {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    flex-wrap: wrap
                }

                .communities-page .pk-cc-icon {
                    width: 42px;
                    height: 42px;
                    border-radius: var(--pk-r-md);
                    display: grid;
                    place-items: center;
                    color: #fff;
                    font-weight: 800;
                    font-size: .9rem;
                    flex: none
                }

                .communities-page .pk-cc-head-txt {
                    flex: 1;
                    min-width: 140px
                }

                .communities-page .pk-cc-name {
                    font-weight: 800;
                    font-size: .96rem;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    flex-wrap: wrap
                }

                .communities-page .pk-cc-meta {
                    font-size: .78rem;
                    color: var(--pk-gray-500);
                    margin-top: 1px
                }

                .communities-page .pk-status-pill {
                    flex: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    font-size: .7rem;
                    font-weight: 700;
                    padding: 5px 10px;
                    border-radius: var(--pk-r-pill);
                    white-space: nowrap
                }

                .communities-page .pk-status-pill svg {
                    width: 11px;
                    height: 11px
                }

                .communities-page .pk-status-public {
                    background: var(--pk-violet-tint);
                    color: var(--pk-violet)
                }

                .communities-page .pk-status-private {
                    background: #EEF0F4;
                    color: var(--pk-gray-700)
                }

                .communities-page .pk-status-paid {
                    background: var(--pk-mint-tint);
                    color: #0D7A45
                }

                .communities-page .pk-status-approval {
                    background: #FCF1DA;
                    color: #946409
                }

                .communities-page .pk-cc-desc {
                    font-size: .86rem;
                    color: var(--pk-gray-700);
                    margin: 10px 0 12px;
                    line-height: 1.55
                }

                .communities-page .pk-cc-foot {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex-wrap: wrap
                }

                .communities-page .pk-cc-members {
                    font-size: .78rem;
                    color: var(--pk-gray-500);
                    flex: 1;
                    min-width: 120px
                }

                .communities-page .pk-tick {
                    width: 13px;
                    height: 13px;
                    flex: none
                }

                .communities-page .pk-rail {
                    display: flex;
                    flex-direction: column;
                    gap: 16px
                }

                .communities-page .pk-rail-card {
                    padding: 16px 18px
                }

                .communities-page .pk-rc-head {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 4px
                }

                .communities-page .pk-rc-head .pk-t {
                    display: flex;
                    align-items: center;
                    gap: 7px;
                    font-weight: 800;
                    font-size: .94rem
                }

                .communities-page .pk-rc-head .pk-t svg {
                    width: 16px;
                    height: 16px;
                    color: var(--pk-gold)
                }

                .communities-page .pk-rc-head .pk-sub {
                    font-size: .76rem;
                    color: var(--pk-gray-500);
                    font-weight: 600
                }

                .communities-page .pk-tr-row {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 9px 0;
                    border-top: 1px solid var(--pk-line)
                }

                .communities-page .pk-tr-row:first-of-type {
                    border-top: none;
                    padding-top: 12px
                }

                .communities-page .pk-tr-rank {
                    font-weight: 800;
                    color: var(--pk-gray-400);
                    width: 16px;
                    flex: none;
                    font-size: .86rem
                }

                .communities-page .pk-tr-ic {
                    width: 32px;
                    height: 32px;
                    border-radius: var(--pk-r-sm);
                    display: grid;
                    place-items: center;
                    color: #fff;
                    font-weight: 700;
                    font-size: .7rem;
                    flex: none
                }

                .communities-page .pk-tr-info {
                    flex: 1;
                    min-width: 0
                }

                .communities-page .pk-tr-info .pk-n {
                    font-weight: 700;
                    font-size: .84rem;
                    color: var(--pk-violet)
                }

                .communities-page .pk-tr-info .pk-m {
                    font-size: .74rem;
                    color: var(--pk-gray-500)
                }

                .communities-page .pk-tr-add {
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    background: #F4F5F9;
                    display: grid;
                    place-items: center;
                    flex: none;
                    color: var(--pk-gray-700);
                    transition: .15s;
                    cursor: pointer;
                    border: none
                }

                .communities-page .pk-tr-add:hover {
                    background: var(--pk-violet-tint);
                    color: var(--pk-violet)
                }

                .communities-page .pk-tr-add svg {
                    width: 14px;
                    height: 14px
                }

                .communities-page .pk-filters-scroll {
                    scrollbar-width: none
                }

                .communities-page .pk-filters-scroll::-webkit-scrollbar {
                    display: none
                }

                .communities-page .modal-dialog {
                    max-height: calc(100vh - 3.5rem)
                }

                .communities-page .modal-content {
                    border: none;
                    border-radius: var(--pk-r-lg);
                    box-shadow: 0 20px 50px -12px rgba(23, 27, 36, .35);
                    max-height: calc(100vh - 3.5rem);
                    overflow: hidden
                }

                /* The <form> sits between modal-content and modal-body/modal-footer,
                                       which breaks Bootstrap's built-in scrollable-modal flex chain
                                       (it expects modal-body to be a direct flex child of modal-content)
    .
                                       Re-establish that chain through the form so only the body scrolls
                                       and the header/footer stay pinned in place. */
                .communities-page .modal-content form {
                    display: flex;
                    flex-direction: column;
                    flex: 1 1 auto;
                    min-height: 0;
                    overflow: hidden
                }

                .communities-page .modal-header {
                    border-bottom: none;
                    padding: 20px 22px 4px;
                    flex: 0 0 auto
                }

                .communities-page .modal-header .modal-title {
                    font-size: 1.08rem;
                    font-weight: 800;
                    color: var(--pk-ink)
                }

                .communities-page .modal-body {
                    padding: 12px 22px 2px;
                    flex: 1 1 auto;
                    min-height: 0;
                    overflow-y: auto
                }

                .communities-page .modal-footer {
                    border-top: none;
                    padding: 14px 22px 22px;
                    flex: 0 0 auto
                }

                .communities-page .modal-footer .pk-btn-violet {
                    flex: 1;
                    justify-content: center
                }

                /* ---- form fields (used inside the modal-body) ---- */

                .communities-page .pk-field {
                    margin-bottom: 16px
                }

                .communities-page .pk-field:last-child {
                    margin-bottom: 0
                }

                .communities-page .pk-field label {
                    display: block;
                    font-size: .8rem;
                    font-weight: 700;
                    margin-bottom: 6px;
                    color: var(--pk-ink)
                }

                .communities-page .pk-field .pk-cnt {
                    font-weight: 600;
                    color: var(--pk-gray-400);
                    float: right
                }

                .communities-page .pk-field input[type=text],
                .communities-page .pk-field input[type=number],
                .communities-page .pk-field textarea,
                .communities-page .pk-field select {
                    width: 100%;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-sm);
                    padding: 10px 12px;
                    font-family: inherit;
                    font-size: .88rem;
                    color: var(--pk-ink);
                    outline: none;
                    transition: .15s;
                    background: #F7F7FB
                }

                .communities-page .pk-field input:focus,
                .communities-page .pk-field textarea:focus,
                .communities-page .pk-field select:focus {
                    border-color: var(--pk-violet);
                    background: #fff
                }

                .communities-page .pk-field textarea {
                    resize: vertical;
                    min-height: 76px;
                    line-height: 1.5
                }

                .communities-page .pk-field select {
                    appearance: none;
                    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%235A4FDC" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>');
                    background-repeat: no-repeat;
                    background-position: right 12px center;
                    background-size: 15px
                }

                .communities-page .pk-field-error {
                    color: var(--pk-red);
                    font-size: .76rem;
                    margin-top: 5px
                }

                /* ---- status option cards (Public / Private / Paid / Approval) ---- */

                .communities-page .pk-status-opt {
                    display: flex;
                    align-items: flex-start;
                    gap: 11px;
                    padding: 11px 12px;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-md);
                    cursor: pointer;
                    transition: .15s;
                    margin-bottom: 8px
                }

                .communities-page .pk-status-opt:last-child {
                    margin-bottom: 0
                }

                .communities-page .pk-status-opt:hover {
                    border-color: var(--pk-gray-400)
                }

                .communities-page .pk-status-opt.pk-sel {
                    border-color: var(--pk-violet);
                    background: var(--pk-violet-tint)
                }

                .communities-page .pk-status-opt input {
                    margin-top: 3px;
                    accent-color: var(--pk-violet);
                    flex: none
                }

                .communities-page .pk-status-opt .pk-so-ic {
                    width: 30px;
                    height: 30px;
                    border-radius: var(--pk-r-sm);
                    display: grid;
                    place-items: center;
                    flex: none
                }

                .communities-page .pk-status-opt .pk-so-ic svg {
                    width: 14px;
                    height: 14px
                }

                .communities-page .pk-status-opt .pk-so-txt b {
                    font-size: .86rem;
                    display: block
                }

                .communities-page .pk-status-opt .pk-so-txt span {
                    font-size: .76rem;
                    color: var(--pk-gray-500)
                }

                /* ---- conditional monthly fee (shown when Paid is selected) ---- */

                .communities-page .pk-price-field {
                    margin-top: -2px;
                    margin-bottom: 8px;
                    padding: 14px;
                    background: var(--pk-mint-tint);
                    border: 1px solid var(--pk-mint-line);
                    border-radius: var(--pk-r-md)
                }

                .communities-page .pk-price-field label {
                    color: #0D7A45
                }

                .communities-page .pk-currency-input {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: #fff;
                    border: 1.3px solid var(--pk-line-strong);
                    border-radius: var(--pk-r-sm);
                    padding: 0 12px
                }

                .communities-page .pk-currency-input span {
                    color: var(--pk-gray-500);
                    font-weight: 700
                }

                .communities-page .pk-currency-input input {
                    border: none;
                    background: none;
                    padding: 10px 0;
                    width: 100%
                }

                /* ---- billing type toggle (One-off / Subscription) ---- */

                .communities-page .pk-billing-toggle {
                    display: flex;
                    gap: 8px;
                    margin-bottom: 12px
                }

                .communities-page .pk-billing-opt {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                    padding: 9px 10px;
                    border: 1.3px solid var(--pk-mint-line);
                    border-radius: var(--pk-r-sm);
                    background: #fff;
                    font-size: .82rem;
                    font-weight: 700;
                    color: var(--pk-gray-700);
                    cursor: pointer
                }

                .communities-page .pk-billing-opt input {
                    accent-color: #0D7A45;
                    margin: 0
                }

                .communities-page .pk-billing-opt.pk-sel {
                    border-color: #0D7A45;
                    background: #fff;
                    color: #0D7A45;
                    box-shadow: 0 0 0 1px #0D7A45 inset
                }

                .communities-page .pk-fee-note {
                    display: flex;
                    align-items: flex-start;
                    gap: 7px;
                    font-size: .78rem;
                    color: #0D7A45;
                    margin-top: 10px;
                    line-height: 1.5
                }

                .communities-page .pk-fee-note svg {
                    width: 14px;
                    height: 14px;
                    flex: none;
                    margin-top: 1px
                }

                .communities-page .pk-fee-payer-row {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    margin-top: 12px
                }

                .communities-page .pk-fee-payer-opt {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    padding: 10px 11px;
                    border: 1.3px solid var(--pk-mint-line);
                    border-radius: var(--pk-r-sm);
                    background: #fff;
                    cursor: pointer;
                    transition: .15s
                }

                .communities-page .pk-fee-payer-opt:hover {
                    border-color: #0D7A45
                }

                .communities-page .pk-fee-payer-opt.pk-sel {
                    border-color: #0D7A45;
                    background: #fff;
                    box-shadow: 0 0 0 1px #0D7A45 inset
                }

                .communities-page .pk-fee-payer-opt input {
                    margin-top: 3px;
                    accent-color: #0D7A45;
                    flex: none
                }

                .communities-page .pk-fee-payer-opt b {
                    font-size: .84rem;
                    display: block;
                    color: var(--pk-ink)
                }

                .communities-page .pk-fee-payer-opt span span {
                    font-size: .76rem;
                    color: var(--pk-gray-500)
                }

                .communities-page .pk-fee-preview {
                    margin-top: 12px;
                    padding: 12px 14px;
                    background: #fff;
                    border: 1px dashed var(--pk-mint-line);
                    border-radius: var(--pk-r-sm)
                }

                .communities-page .pk-fp-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    font-size: .82rem;
                    padding: 5px 0;
                    color: var(--pk-gray-700)
                }

                .communities-page .pk-fp-row b {
                    font-family: 'Space Mono', ui-monospace, monospace;
                    color: var(--pk-ink);
                    font-weight: 700
                }

                .communities-page .pk-fp-row.pk-fp-total {
                    border-top: 1px solid var(--pk-mint-line);
                    margin-top: 3px;
                    padding-top: 8px
                }

                .communities-page .pk-fp-row.pk-fp-total b {
                    color: #0D7A45;
                    font-size: .92rem
                }

                /* ---- info note at the bottom of the modal ---- */

                .communities-page .pk-modal-note {
                    display: flex;
                    align-items: center;
                    gap: 7px;
                    font-size: .76rem;
                    color: var(--pk-gray-500);
                    margin-bottom: 8px
                }

                .communities-page .pk-modal-note svg {
                    width: 13px;
                    height: 13px;
                    flex: none
                }
            </style>
        @endverbatim

        {{-- ============ INFO BANNER ============ --}}
        <div class="pk-card pk-banner d-flex flex-column flex-md-row align-items-md-center gap-3">
            <div class="pk-banner-ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="8" r="3" />
                    <path d="M2 20a7 7 0 0 1 14 0" />
                    <circle cx="17.5" cy="9.5" r="2.5" />
                    <path d="M15.5 13a5.5 5.5 0 0 1 6.5 5.4" />
                </svg>
            </div>
            <div class="pk-banner-copy flex-grow-1">
                <h2>Build a community, earn together</h2>
                <p>Group up with creators around a shared topic. Members can chat, post, and grow their engagement
                    earnings side by side.</p>
            </div>
            <div class="d-grid d-md-block">
                <button type="button" class="pk-btn pk-btn-violet" data-bs-toggle="modal"
                    data-bs-target="#createCommunityModal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                    </svg>
                    Create community
                </button>
            </div>
        </div>

        <div class="row g-3 g-lg-4">

            {{-- ============ FEED COLUMN ============ --}}
            <main class="col-12 col-lg-8 order-2 order-lg-1">
                <div class="pk-sec-head">
                    <h3>All communities</h3>
                </div>

                <div class="pk-search-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search communities…" />
                </div>

                <div class="d-flex flex-nowrap overflow-auto gap-2 pb-2 mb-2 pk-filters-scroll">

                    <button type="button" class="pk-f-chip @if ($filter === 'all') pk-sel @endif"
                        wire:click="setFilter('all')">All</button>

                    <button type="button" class="pk-f-chip @if ($filter === 'joined') pk-sel @endif"
                        wire:click="setFilter('joined')">Joined</button>

                    <button type="button" class="pk-f-chip @if ($filter === 'mine') pk-sel @endif"
                        wire:click="setFilter('mine')">My Communities</button>

                    @foreach ($category as $cat)
                        <button type="button" class="pk-f-chip @if ($filter === $cat->id) pk-sel @endif"
                            wire:click="setFilter('{{ $cat->id }}')">{{ $cat->name }}</button>
                    @endforeach
                </div>

                <div class="pk-comm-list">

                    @forelse ($communities as $community)
                        <article class="pk-card pk-comm-card" wire:key="community-{{ $community->id }}">
                            <div class="pk-cc-top">
                                <a href="{{ route('community.show', $community) }}" wire:navigate
                                    class="d-flex align-items-start gap-3 flex-grow-1 text-decoration-none text-reset"
                                    style="min-width:0">
                                    <div class="pk-cc-icon" style="background:{{ $community->color }}">
                                        {{ $community->initials }}</div>
                                    <div class="pk-cc-head-txt">
                                        <div class="pk-cc-name">{{ $community->name }}</div>
                                        <div class="pk-cc-meta">{{ $community->category->name ?? 'Uncategorised' }} ·
                                            {{ number_format($community->members_count) }} members</div>
                                    </div>
                                </a>

                                @switch($community->type)
                                    @case('public')
                                        <span class="pk-status-pill pk-status-public">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z" />
                                            </svg>
                                            Public
                                        </span>
                                    @break

                                    @case('private')
                                        <span class="pk-status-pill pk-status-private">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                                <rect x="5" y="11" width="14" height="9" rx="2" />
                                                <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                            </svg>
                                            Invite only
                                        </span>
                                    @break

                                    @case('paid')
                                        <span class="pk-status-pill pk-status-paid">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                                {{-- <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                                                    stroke-linecap="round" /> --}}
                                            </svg>
                                            {{ getCurrencyCode() }}{{ number_format(convertCurrency($community->member_charge, $community->currency, auth()->user()->wallet->currency), 2) }}
                                            {{ $community->price_suffix }}
                                            {{-- &#8358;{{ number_format($community->member_charge, 2) }} --}}
                                        </span>
                                    @break

                                    @case('approval')
                                        <span class="pk-status-pill pk-status-approval">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M12 7v5l3 2" />
                                            </svg>
                                            Approval
                                        </span>
                                    @break
                                @endswitch
                            </div>

                            <p class="pk-cc-desc">{{ Str::limit($community->description, 140) }}</p>

                            <div class="pk-cc-foot">
                                <span class="pk-cc-members">{{ number_format($community->members_count) }}
                                    members</span>

                                @if ($community->is_member)
                                    <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" disabled>
                                        {{ $community->type === 'private' ? 'Invite only' : 'Joined' }}
                                    </button>
                                @elseif ($community->type === 'private')
                                    <button type="button" class="pk-btn pk-btn-outline pk-btn-sm" disabled>Invite
                                        only</button>
                                @elseif ($community->type === 'approval')
                                    <button type="button" class="pk-btn pk-btn-outline pk-btn-sm">Request to
                                        join</button>
                                @elseif ($community->type === 'paid')
                                    {{-- {{ $this->userSubscriptionStatus($community->id) }} --}}

                                    @if ($this->userSubscriptionStatus($community->id) === 'active')
                                        <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                            disabled>Joined</button>
                                    @elseif ($this->userSubscriptionStatus($community->id) === 'pending')
                                        <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                            disabled>Pending</button>
                                    @else
                                        <a href="{{ url('community/payment/' . $community->id) }}"
                                            class="pk-btn pk-btn-violet pk-btn-sm" {{-- wire:click.prevent="subscribe('{{ $community->id }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="subscribe('{{ $community->id }}') --}} ">
                                            {{ $community->billing_type === 'one_off' ? 'Pay once' : 'Subscribe' }}
                                        </a>
                                        {{-- <button type="button" class="pk-btn pk-btn-violet pk-btn-sm"
                                            wire:click="subscribe('{{ $community->id }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="subscribe('{{ $community->id }}')">
                                            {{ $community->billing_type === 'one_off' ? 'Pay once' : 'Subscribe' }}
                                        </button> --}}
 @endif

                                            {{-- @if ($this->hasPendingSubscription($community->id))
                                        <button type="button" class="pk-btn pk-btn-outline pk-btn-sm"
                                            disabled>Pending</button>
                                    @else
                                        <button type="button" class="pk-btn pk-btn-violet pk-btn-sm"
                                            wire:click="subscribe('{{ $community->id }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="subscribe('{{ $community->id }}')">
                                            {{ $community->billing_type === 'one_off' ? 'Pay once' : 'Subscribe' }}
                                        </button>
                                    @endif --}}
                                            {{-- <button type="button" class="pk-btn pk-btn-violet pk-btn-sm">Subscribe</button> --}}
                                        @else
                                            <button type="button" class="pk-btn pk-btn-violet pk-btn-sm"
                                                wire:click="join('{{ $community->id }}')"
                                                wire:loading.attr="disabled"
                                                wire:target="join('{{ $community->id }}')">Join</button>
                                    @endif
                            </div>
                        </article>
                        @empty
                            <div class="pk-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <circle cx="9" cy="8" r="3" />
                                    <path d="M2 20a7 7 0 0 1 14 0" />
                                    <circle cx="17.5" cy="9.5" r="2.5" />
                                    <path d="M15.5 13a5.5 5.5 0 0 1 6.5 5.4" />
                                </svg>
                                <b>No communities match yet</b>
                                Try another filter or search term, or be the first to start one.
                            </div>
                        @endforelse

                    </div>

                    @if ($communities->hasMorePages())
                        <div class="d-grid d-sm-flex justify-content-sm-center pk-load-more-row">
                            <button type="button" class="pk-btn pk-btn-outline" wire:click="loadMore"
                                wire:loading.attr="disabled" wire:target="loadMore">
                                <span wire:loading.remove wire:target="loadMore">Load more communities</span>
                                <span wire:loading wire:target="loadMore">Loading…</span>
                            </button>
                        </div>
                    @endif
                </main>

                {{-- ============ RIGHT RAIL ============ --}}
                <aside class="col-12 col-lg-4 order-1 order-lg-2 d-flex flex-column gap-3">
                    <div class="pk-card pk-rail-card">
                        <div class="pk-rc-head">
                            <span class="pk-t">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M13.5 2.5c1 3-1 4.5-2 6-1.3 2-1.5 3.5-.5 5a3 3 0 0 0 5.7-1.2c.9.9 1.3 2 1.3 3.2a6 6 0 0 1-12 0c0-4 2.6-6 3.3-8.4.4-1.4.2-3-.8-4.6Z" />
                                </svg>
                                Trending Communities
                            </span>
                            <span class="pk-sub">Top {{ $trending->count() }}</span>
                        </div>
                        @forelse ($trending as $i => $trendingCommunity)
                            <div class="pk-tr-row" wire:key="trending-{{ $trendingCommunity->id }}">
                                <span class="pk-tr-rank">{{ $i + 1 }}</span>
                                <div class="pk-tr-ic" style="background:{{ $trendingCommunity->color }}">
                                    {{ $trendingCommunity->initials }}</div>
                                <div class="pk-tr-info">
                                    <div class="pk-n">{{ $trendingCommunity->name }}</div>
                                    <div class="pk-m">{{ number_format($trendingCommunity->members_count) }} members
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="pk-tr-row"><span class="pk-m">No communities yet.</span></div>
                        @endforelse
                    </div>

                    <div class="pk-card pk-rail-card">
                        <div class="pk-rc-head">
                            <span class="pk-t">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="8" cy="8" r="3" />
                                    <circle cx="17" cy="9" r="2.5" />
                                    <path d="M2 20a6.5 6.5 0 0 1 12 0M14 20a5 5 0 0 1 8-3.8" />
                                </svg>
                                Suggested for you
                            </span>
                        </div>
                        @forelse ($suggested as $suggestedCommunity)
                            <div class="pk-tr-row" wire:key="suggested-{{ $suggestedCommunity->id }}">
                                <div class="pk-tr-ic" style="background:{{ $suggestedCommunity->color }}">
                                    {{ $suggestedCommunity->initials }}</div>
                                <div class="pk-tr-info">
                                    <div class="pk-n">{{ $suggestedCommunity->name }}</div>
                                    <div class="pk-m">{{ number_format($suggestedCommunity->members_count) }} members
                                    </div>
                                </div>
                                <button type="button" class="pk-tr-add"
                                    wire:click="join('{{ $suggestedCommunity->id }}')" wire:loading.attr="disabled"
                                    wire:target="join('{{ $suggestedCommunity->id }}')"
                                    aria-label="Join {{ $suggestedCommunity->name }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M20 8v6M23 11h-6" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="pk-tr-row"><span class="pk-m">Nothing to suggest right now.</span></div>
                        @endforelse
                    </div>
                </aside>
            </div>

            {{-- ============ CREATE COMMUNITY MODAL ============ --}}
            {{--
            wire:ignore.self is the fix for the "modal collapses after filling a field" bug.
            Every wire:model request re-renders this component. Bootstrap manages the
            modal's visible state (the "show" class + inline display style) by mutating
            this exact element with plain JS, outside of Livewire/Blade. Without
            wire:ignore.self, Livewire's morph diff sees that the freshly rendered HTML
            for THIS element doesn't have those Bootstrap-added attributes and strips
            them — which is what makes the modal appear to close on every keystroke/blur.
            wire:ignore.self tells Livewire "don't touch this element's own attributes",
            while still letting everything INSIDE it (errors, the fee field, etc.) update
            normally.
        --}}
            <div class="modal fade" id="createCommunityModal" tabindex="-1" aria-labelledby="createCommunityTitle"
                aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title" id="createCommunityTitle">Create a community</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"
                                aria-label="Close"></button>
                        </div>

                        <form wire:submit.prevent="createCommunity">
                            <div class="modal-body">
                                <div class="pk-field">
                                    <label for="cName">Community name</label>
                                    <input type="text" id="cName" maxlength="255" wire:model.blur="name"
                                        placeholder="e.g. Side Hustle Naija" />
                                    @error('name')
                                        <div class="pk-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="pk-field">
                                    <label for="cDesc">Description <span
                                            class="pk-cnt">{{ strlen($description) }}/1000</span></label>
                                    <textarea id="cDesc" maxlength="1000" wire:model.live.debounce.300ms="description"
                                        placeholder="What's this community for, and who should join?"></textarea>
                                    @error('description')
                                        <div class="pk-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="pk-field">
                                    <label for="cCat">Category</label>
                                    <select id="cCat" wire:model="community_categories_id">
                                        <option value="" disabled selected>Select a category</option>
                                        @foreach ($category as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('community_categories_id')
                                        <div class="pk-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="pk-field">
                                    <label>Status</label>

                                    <label class="pk-status-opt @if ($type === 'public') pk-sel @endif">
                                        <input type="radio" name="cStatus" value="public" wire:model.live="type" />
                                        <span class="pk-so-ic"
                                            style="background:var(--pk-violet-tint);color:var(--pk-violet)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.6">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z" />
                                            </svg>
                                        </span>
                                        <span class="pk-so-txt"><b>Public</b><span>Anyone can find and join
                                                instantly.</span></span>
                                    </label>

                                    <label class="pk-status-opt @if ($type === 'private') pk-sel @endif">
                                        <input type="radio" name="cStatus" value="private" wire:model.live="type" />
                                        <span class="pk-so-ic" style="background:#EEF0F4;color:var(--pk-gray-700)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.6">
                                                <rect x="5" y="11" width="14" height="9" rx="2" />
                                                <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                                            </svg>
                                        </span>
                                        <span class="pk-so-txt"><b>Private (invite only)</b><span>Hidden from search —
                                                only people you invite can join.</span></span>
                                    </label>

                                    <label class="pk-status-opt @if ($type === 'paid') pk-sel @endif">
                                        <input type="radio" name="cStatus" value="paid" wire:model.live="type" />
                                        <span class="pk-so-ic" style="background:var(--pk-mint-tint);color:#0D7A45">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.6">
                                                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                                                    stroke-linecap="round" />
                                            </svg>
                                        </span>
                                        <span class="pk-so-txt"><b>Paid</b>
                                            <span>
                                                Members pay to join — either a one-time payment, or a recurring
                                                subscription. You choose below.
                                            </span>
                                        </span>
                                    </label>

                                    {{-- @if ($type === 'paid')
                                        <div class="pk-price-field">
                                            {{-- $basePrice = convertToBaseCurrency($level->amount, auth()->user()->wallet->currency); --
                                            <label>How should members pay?</label>
                                            <div class="pk-billing-toggle">
                                                @if (userBaseCurrency() === 'NGN')
                                                    <label
                                                        class="pk-billing-opt @if ($billing_type === 'one_off') pk-sel @endif">
                                                        <input type="radio" name="cBillingType" value="one_off" 
                                                            wire:model.live="billing_type">
                                                        One-off payment
                                                    </label>
                                                @else
                                                    <label
                                                        class="pk-billing-opt @if ($billing_type === 'one_off') pk-sel @endif">
                                                        <input type="radio" name="cBillingType" value="one_off"
                                                            wire:model.live="billing_type">
                                                        One-off payment
                                                    </label>
                                                    <label
                                                        class="pk-billing-opt @if ($billing_type === 'subscription') pk-sel @endif">
                                                        <input type="radio" name="cBillingType" value="subscription"
                                                            wire:model.live="billing_type">
                                                        Subscription
                                                    </label>
                                                @endif


                                            </div>
                                            @error('billing_type')
                                                <div class="pk-field-error">{{ $message }}</div>
                                            @enderror
                                            

                                            @if ($billing_type === 'subscription')
                                                <div class="pk-field" style="margin-bottom:12px">
                                                    <label for="cInterval">Billing interval</label>
                                                    <select id="cInterval" wire:model.live="billing_interval">
                                                        @foreach (config('community.billing_intervals', []) as $key => $meta)
                                                            <option value="{{ $key }}">{{ $meta['label'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('billing_interval')
                                                        <div class="pk-field-error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <label for="cFee">
                                                {{ $billing_type === 'one_off' ? 'Price (one-time)' : 'Price per billing cycle' }}
                                            </label>
                                            @php($preview = $this->feePreview())

                                            <?php
                                            
                                            $userBaseCurrency = userBaseCurrency();
                                            if ($userBaseCurrency == 'USD') {
                                                $placehoder = 10;
                                            } else {
                                                $placehoder = 2500;
                                            }
                                            
                                            ?>
                                            <div class="pk-currency-input">
                                                <span>{{ getCurrencyCode() }}</span>
                                                <input type="number" id="cFee" min="100" step="50"
                                                    wire:model.live.debounce.400ms="monthly_fee"
                                                    placeholder="{{ $placehoder }}" />
                                            </div>
                                            @error('monthly_fee')
                                                <div class="pk-field-error">{{ $message }}</div>
                                            @enderror

                                            <div class="pk-fee-note">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <path d="M12 16v-4M12 8h.01" />
                                                </svg>
                                                <span>Payhankey charges a <strong>{{ $platformFeePercent }}%</strong>
                                                    platform fee on every payment made into a paid community.</span>
                                            </div>

                                            <div class="pk-fee-payer-row">
                                                <label
                                                    class="pk-fee-payer-opt @if ($fee_payer === 'creator') pk-sel @endif">
                                                    <input type="radio" name="cFeePayer" value="creator"
                                                        wire:model.live="fee_payer" />
                                                    <span><b>I'll cover the {{ $platformFeePercent }}% fee</b>
                                                        <span>It's deducted from what you receive — members
                                                            pay exactly the amount above.</span>
                                                    </span>
                                                </label>
                                                <label
                                                    class="pk-fee-payer-opt @if ($fee_payer === 'members') pk-sel @endif">
                                                    <input type="radio" name="cFeePayer" value="members"
                                                        wire:model.live="fee_payer" />
                                                    <span><b>My members will cover it</b>
                                                        <span>The fee is added on top, so you still receive the full
                                                            amount above.</span>
                                                    </span>
                                                </label>
                                            </div>
                                            @error('fee_payer')
                                                <div class="pk-field-error">{{ $message }}</div>
                                            @enderror


                                            @if ($preview)
                                                <div class="pk-fee-preview">
                                                    <div class="pk-fp-row">
                                                        <span>Members pay{{ $preview['suffix'] }}</span>
                                                        <b>{{ getCurrencyCode() }}{{ number_format($preview['memberCharge'], 2) }}
                                                        </b>
                                                    </div>
                                                    <div class="pk-fp-row">
                                                        <span>Payhankey fee ({{ $platformFeePercent }}%)</span>
                                                        <b>{{ getCurrencyCode() }}{{ number_format($preview['platformCut'], 2) }}
                                                        </b>
                                                    </div>
                                                    <div class="pk-fp-row pk-fp-total">
                                                        <span>You receive{{ $preview['suffix'] }}</span>
                                                        <b>{{ getCurrencyCode() }}{{ number_format($preview['creatorPayout'], 2) }}
                                                        </b>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif --}}

                                   
                                    @if ($type === 'paid')
                                        <div class="pk-price-field">
                                            <label>How should members pay?</label>

                                            <div class="pk-billing-toggle">
                                                {{-- One-off is always available --}}
                                                <label
                                                    class="pk-billing-opt @if ($billing_type === 'one_off') pk-sel @endif">
                                                    <input type="radio" name="cBillingType" value="one_off"
                                                        wire:model.live="billing_type">
                                                    One-off payment
                                                </label>

                                                {{-- Subscription is only available when base currency is NOT NGN --}}
                                                @if (userBaseCurrency() !== 'NGN')
                                                    <label
                                                        class="pk-billing-opt @if ($billing_type === 'subscription') pk-sel @endif">
                                                        <input type="radio" name="cBillingType" value="subscription"
                                                            wire:model.live="billing_type">
                                                        Subscription
                                                    </label>
                                                @endif
                                            </div>

                                            @error('billing_type')
                                                <div class="pk-field-error">{{ $message }}</div>
                                            @enderror

                                            {{-- Billing interval is only available for subscriptions --}}
                                            @if ($billing_type === 'subscription' && userBaseCurrency() !== 'NGN')
                                                <div class="pk-field" style="margin-bottom:12px">
                                                    <label for="cInterval">Billing interval</label>

                                                    <select id="cInterval" wire:model.live="billing_interval">
                                                        @foreach (config('community.billing_intervals', []) as $key => $meta)
                                                            <option value="{{ $key }}">
                                                                {{ $meta['label'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @error('billing_interval')
                                                        <div class="pk-field-error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <label for="cFee">
                                                {{ $billing_type === 'one_off' ? 'Price (one-time)' : 'Price per billing cycle' }}
                                            </label>

                                            @php($preview = $this->feePreview())

                                            <?php
                                            $userBaseCurrency = userBaseCurrency();
                                            
                                            if ($userBaseCurrency === 'USD') {
                                                $placehoder = 10;
                                            } else {
                                                $placehoder = 2500;
                                            }
                                            ?>

                                            <div class="pk-currency-input">
                                                <span>{{ getCurrencyCode() }}</span>
                                                <input type="number" id="cFee" min="100" step="50"
                                                    wire:model.live.debounce.400ms="monthly_fee"
                                                    placeholder="{{ $placehoder }}" />
                                            </div>

                                            @error('monthly_fee')
                                                <div class="pk-field-error">{{ $message }}</div>
                                            @enderror

                                            <div class="pk-fee-note">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <path d="M12 16v-4M12 8h.01" />
                                                </svg>

                                                <span>
                                                    Payhankey charges a <strong>{{ $platformFeePercent }}%</strong>
                                                    platform fee on every payment made into a paid community.
                                                </span>
                                            </div>

                                            <div class="pk-fee-payer-row">
                                                <label
                                                    class="pk-fee-payer-opt @if ($fee_payer === 'creator') pk-sel @endif">
                                                    <input type="radio" name="cFeePayer" value="creator"
                                                        wire:model.live="fee_payer" />

                                                    <span>
                                                        <b>I'll cover the {{ $platformFeePercent }}% fee</b>
                                                        <span>
                                                            It's deducted from what you receive — members pay exactly
                                                            the amount above.
                                                        </span>
                                                    </span>
                                                </label>

                                                <label
                                                    class="pk-fee-payer-opt @if ($fee_payer === 'members') pk-sel @endif">
                                                    <input type="radio" name="cFeePayer" value="members"
                                                        wire:model.live="fee_payer" />

                                                    <span>
                                                        <b>My members will cover it</b>
                                                        <span>
                                                            The fee is added on top, so you still receive the full
                                                            amount above.
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>

                                            @error('fee_payer')
                                                <div class="pk-field-error">{{ $message }}</div>
                                            @enderror

                                            @if ($preview)
                                                <div class="pk-fee-preview">
                                                    <div class="pk-fp-row">
                                                        <span>Members pay{{ $preview['suffix'] }}</span>
                                                        <b>
                                                            {{ getCurrencyCode() }}{{ number_format($preview['memberCharge'], 2) }}
                                                        </b>
                                                    </div>

                                                    <div class="pk-fp-row">
                                                        <span>Payhankey fee ({{ $platformFeePercent }}%)</span>
                                                        <b>
                                                            {{ getCurrencyCode() }}{{ number_format($preview['platformCut'], 2) }}
                                                        </b>
                                                    </div>

                                                    <div class="pk-fp-row pk-fp-total">
                                                        <span>You receive{{ $preview['suffix'] }}</span>
                                                        <b>
                                                            {{ getCurrencyCode() }}{{ number_format($preview['creatorPayout'], 2) }}
                                                        </b>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <label class="pk-status-opt @if ($type === 'approval') pk-sel @endif">
                                        <input type="radio" name="cStatus" value="approval" wire:model.live="type" />
                                        <span class="pk-so-ic" style="background:#FCF1DA;color:#946409">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.6">
                                                <circle cx="12" cy="12" r="9" />
                                                <path d="M12 7v5l3 2" />
                                            </svg>
                                        </span>
                                        <span class="pk-so-txt"><b>Approval required</b><span>Visible to everyone, but
                                                joining needs admin acceptance.</span></span>
                                    </label>
                                    @error('type')
                                        <div class="pk-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="pk-modal-note">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 16v-4M12 8h.01" />
                                    </svg>
                                    You'll be the community's first admin. You can add co-admins and change the status
                                    later from settings.
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="pk-btn pk-btn-outline" data-bs-dismiss="modal"
                                    wire:click="resetForm">Cancel</button>
                                <button type="submit" class="pk-btn pk-btn-violet" wire:loading.attr="disabled"
                                    wire:target="createCommunity">
                                    <span wire:loading.remove wire:target="createCommunity">Create community</span>
                                    <span wire:loading wire:target="createCommunity">Creating…</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{--
            After a successful create, Bootstrap's own JS should close the modal too
            (not just reset the Livewire state), since resetForm() alone won't hide it.
        --}}
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('community-created', () => {
                        var modalEl = document.getElementById('createCommunityModal');
                        var instance = bootstrap.Modal.getOrCreateInstance(modalEl);
                        instance.hide();
                    });
                });
            </script>

        </div>
    </div>
