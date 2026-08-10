<?php

namespace App\Livewire\User;

use App\Models\Transaction;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionList extends Component
{
    use WithPagination;

    #[Url(history: true, as: 'q')]
    public string $search = '';

    #[Url(history: true, as: 'status')]
    public string $statusFilter = '';

    #[Url(history: true, as: 'from')]
    public string $dateFrom = '';

    #[Url(history: true, as: 'to')]
    public string $dateTo = '';

    #[Url(history: true, as: 'sort')]
    public string $sortField = 'created_at';

    #[Url(history: true, as: 'dir')]
    public string $sortDirection = 'desc';

    public int $perPage = 10;

    private const CURRENCY_SYMBOLS = [
        'NGN' => '₦', 'USD' => '$', 'GBP' => '£', 'EUR' => '€',
        'GHS' => '₵', 'KES' => 'KSh', 'ZAR' => 'R',
    ];

    private const SORTABLE = ['created_at', 'amount'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE, true)) return;

        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'desc';

        $this->sortField = $field;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function currencySymbol(?string $code): string
    {
        $code = strtoupper((string) $code);
        return self::CURRENCY_SYMBOLS[$code] ?? ($code !== '' ? $code.' ' : '');
    }

    public function statusMeta(string $status): array
    {
        return match (strtolower($status)) {
            'successful', 'success', 'active' => ['label' => 'Successful', 'class' => 'pk-status-paid'],
            'pending', 'processing' => ['label' => 'Pending', 'class' => 'pk-status-approval'],
            'failed', 'declined' => ['label' => 'Failed', 'class' => 'pk-status-failed'],
            default => ['label' => ucfirst($status), 'class' => 'pk-status-private'],
        };
    }

    private function baseQuery()
    {
        return Transaction::query()->where('user_id', auth()->id());
    }

    public function render()
    {
        $transactions = $this->baseQuery()
            ->select(['id', 'ref', 'amount', 'currency', 'description', 'status', 'created_at'])
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('ref', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('currency', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom !== '', fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage); // <-- must be paginate(), never get()

        $stats = $this->baseQuery()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('livewire.user.transaction-list', [
            'transactions' => $transactions, // LengthAwarePaginator, has hasPages()
            'stats' => $stats,
        ]);
    }
}