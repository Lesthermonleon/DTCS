{{--
    Skeleton: Full Dashboard Page
    Usage: <x-skeleton.dashboard :stats="4" :cards="2" />
    Props:
      $stats = number of stat cards (default 4)
      $cards = number of table/content cards below (default 2)
      $colors = array of accent colors for stat cards
--}}
@props([
    'stats'  => 4,
    'cards'  => 2,
    'colors' => ['signal','steel','amber','coral'],
])

<div role="status" aria-label="Loading dashboard" aria-busy="true">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="sk sk-2xl sk-w-100 mb-2" style="width:200px;border-radius:6px;"></div>
            <div class="sk sk-sm" style="width:280px;border-radius:6px;"></div>
        </div>
        <div class="sk sk-xl sk-rounded sk-hide-mobile" style="width:130px;"></div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        @for ($i = 0; $i < $stats; $i++)
        <div class="{{ $stats === 3 ? 'col-md-4' : 'col-6 col-md-3' }}">
            <x-skeleton.stat-card :color="$colors[$i % count($colors)]" />
        </div>
        @endfor
    </div>

    {{-- Content cards (tables / charts) --}}
    <div class="row g-3">
        @if($cards === 1)
        <div class="col-12">
            <x-skeleton.table :rows="6" :cols="5" />
        </div>
        @elseif($cards === 2)
        <div class="col-md-6">
            <x-skeleton.table :rows="5" :cols="4" :pagination="false" />
        </div>
        <div class="col-md-6">
            <x-skeleton.table :rows="5" :cols="4" :pagination="false" />
        </div>
        @elseif($cards >= 3)
        <div class="col-12">
            <x-skeleton.table :rows="6" :cols="5" />
        </div>
        <div class="col-md-6">
            <x-skeleton.card :count="2" />
        </div>
        <div class="col-md-6">
            <x-skeleton.card :count="2" />
        </div>
        @endif
    </div>

</div>
