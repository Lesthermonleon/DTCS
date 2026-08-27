{{-- ── Summary Cards Partial ── --}}
{{-- Usage: @include('reports._summary-cards', ['cards' => [['label'=>'Total', 'value'=>100, 'icon'=>'bi-list', 'color'=>'primary'], ...]]) --}}
@php
    $cardCount = count($cards ?? []);
    $gridClass = 'balanced-grid-' . min(max($cardCount, 1), 8);
@endphp
<div class="balanced-grid {{ $gridClass }} mb-4">
    @foreach($cards as $card)
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] }} bg-opacity-10"
                 style="width:44px;height:44px;flex-shrink:0;">
                <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }}" style="font-size:1.15rem;"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 lh-1">{{ number_format($card['value']) }}</div>
                <div class="text-muted small">{{ $card['label'] }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>
