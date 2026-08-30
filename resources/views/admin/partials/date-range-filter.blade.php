@php
    /** @var \App\Support\AdminDateRange $dateRange */
    $routeName = $routeName ?? Route::currentRouteName();
    $routeParams = $routeParams ?? [];
    $extraQuery = $extraQuery ?? [];
    $presetQuery = fn (string $rangeKey) => array_merge($routeParams, $extraQuery, ['range' => $rangeKey]);
@endphp

<style>
    .dash-range {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .65rem;
        margin-bottom: 1.25rem;
        padding: .85rem 1rem;
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius);
        box-shadow: var(--dash-shadow);
    }
    .dash-range__presets {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin-right: .25rem;
    }
    .dash-range__btn {
        display: inline-flex;
        align-items: center;
        padding: .4rem .75rem;
        border-radius: 999px;
        border: 1px solid var(--dash-border);
        background: #fff;
        color: var(--dash-muted);
        font-size: .8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: background .15s, border-color .15s, color .15s;
    }
    .dash-range__btn:hover { background: #f8fafc; color: var(--dash-text); }
    .dash-range__btn.is-active {
        background: var(--dash-accent-soft);
        border-color: #c7d2fe;
        color: #4338ca;
    }
    .dash-range__label {
        font-size: .8125rem;
        color: var(--dash-muted);
        margin-left: auto;
        font-weight: 500;
    }
    .dash-range input[type="date"].dash-input {
        flex: 0 0 auto;
        min-width: 145px;
    }
    @media (max-width: 720px) {
        .dash-range__label { width: 100%; margin-left: 0; }
    }
</style>

<form method="get" action="{{ route($routeName, $routeParams) }}" class="dash-range">
    <div class="dash-range__presets">
        @foreach (\App\Support\AdminDateRange::OPTIONS as $key => $label)
            @continue($key === 'custom')
            <a href="{{ route($routeName, $presetQuery($key)) }}"
               class="dash-range__btn{{ $dateRange->key === $key ? ' is-active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
        <span class="dash-range__btn{{ $dateRange->key === 'custom' ? ' is-active' : '' }}" style="cursor:default">Custom</span>
    </div>

    <input type="hidden" name="range" value="custom">
    @foreach ($extraQuery as $extraKey => $extraValue)
        @if ($extraValue !== null && $extraValue !== '')
            <input type="hidden" name="{{ $extraKey }}" value="{{ $extraValue }}">
        @endif
    @endforeach
    <input type="date" name="from" value="{{ $dateRange->start->toDateString() }}" class="dash-input" required>
    <input type="date" name="to" value="{{ $dateRange->end->toDateString() }}" class="dash-input" required>
    <button type="submit" class="dash-btn dash-btn--primary">Apply</button>

    <span class="dash-range__label">Showing {{ $dateRange->label() }}</span>
</form>
