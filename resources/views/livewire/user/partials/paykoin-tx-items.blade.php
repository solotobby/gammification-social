@foreach ($transactions as $tx)
    @php
        $icons = [
            'topup' => 'fa-plus-circle',
            'gift_sent' => 'fa-gift',
            'gift_received' => 'fa-heart',
            'convert' => 'fa-exchange',
        ];
        $titles = [
            'topup' => 'Top-up',
            'gift_sent' => 'Gift sent',
            'gift_received' => 'Gift received',
            'convert' => 'Converted to wallet',
        ];
        $subtitle = $tx['description'] ?? '';
        if ($tx['type'] === 'convert' && $tx['fiat_amount']) {
            $subtitle = $pkSymbol.number_format((float) $tx['fiat_amount'], $pkCurrency === 'NGN' ? 0 : 2).' · Main wallet';
        }
        if ($tx['type'] === 'topup' && $tx['fiat_amount']) {
            $subtitle = 'Korapay · '.$pkSymbol.number_format((float) $tx['fiat_amount'], $pkCurrency === 'NGN' ? 0 : 2);
        }
    @endphp
    <li class="pkoin-tx-item">
        <span class="pkoin-tx-icon pkoin-tx-icon--{{ $tx['type'] }}">
            <i class="fa {{ $icons[$tx['type']] ?? 'fa-circle' }}"></i>
        </span>
        <div class="pkoin-tx-main">
            <b>{{ $titles[$tx['type']] ?? ucfirst($tx['type']) }}</b>
            <small>{{ $subtitle }}</small>
        </div>
        <div class="pkoin-tx-right">
            <span class="pkoin-tx-amount {{ $tx['pk_amount'] > 0 ? 'is-credit' : 'is-debit' }}">
                {{ $tx['pk_amount'] > 0 ? '+' : '' }}{{ number_format($tx['pk_amount']) }} PK
            </span>
            <time>{{ $tx['created_at'] }}</time>
        </div>
    </li>
@endforeach
