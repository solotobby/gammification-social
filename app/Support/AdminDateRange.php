<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminDateRange
{
    public const OPTIONS = [
        'today' => 'Today',
        '7d' => '7 days',
        '30d' => '30 days',
        '60d' => '60 days',
        'custom' => 'Custom',
    ];

    public function __construct(
        public readonly string $key,
        public readonly Carbon $start,
        public readonly Carbon $end,
    ) {}

    public static function fromRequest(Request $request, string $default = '30d'): self
    {
        $key = $request->string('range')->trim()->toString() ?: $default;

        if (! array_key_exists($key, self::OPTIONS)) {
            $key = $default;
        }

        $end = now()->endOfDay();

        return match ($key) {
            'today' => new self('today', now()->startOfDay(), $end),
            '7d' => new self('7d', now()->subDays(6)->startOfDay(), $end),
            '30d' => new self('30d', now()->subDays(29)->startOfDay(), $end),
            '60d' => new self('60d', now()->subDays(59)->startOfDay(), $end),
            default => self::fromCustomDates(
                $request->input('from'),
                $request->input('to'),
            ),
        };
    }

    protected static function fromCustomDates(mixed $from, mixed $to): self
    {
        $start = $from
            ? Carbon::parse($from)->startOfDay()
            : now()->subDays(29)->startOfDay();
        $end = $to
            ? Carbon::parse($to)->endOfDay()
            : now()->endOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        if ($start->diffInDays($end) > 365) {
            $start = $end->copy()->subDays(365)->startOfDay();
        }

        return new self('custom', $start, $end);
    }

    public function days(): int
    {
        return max(1, (int) $this->start->copy()->startOfDay()->diffInDays($this->end->copy()->startOfDay()) + 1);
    }

    public function label(): string
    {
        if ($this->key === 'custom') {
            return $this->start->format('M j, Y').' – '.$this->end->format('M j, Y');
        }

        return self::OPTIONS[$this->key] ?? 'Selected range';
    }

    public function cacheKey(): string
    {
        return $this->key.':'.$this->start->toDateString().':'.$this->end->toDateString();
    }

    public function queryParams(): array
    {
        $params = ['range' => $this->key];

        if ($this->key === 'custom') {
            $params['from'] = $this->start->toDateString();
            $params['to'] = $this->end->toDateString();
        }

        return $params;
    }

    public function apply(Builder $query, string $column = 'created_at'): Builder
    {
        return $query->whereBetween($column, [$this->start, $this->end]);
    }

    public function applyDate(Builder $query, string $column = 'date'): Builder
    {
        return $query->whereBetween($column, [
            $this->start->toDateString(),
            $this->end->toDateString(),
        ]);
    }
}
