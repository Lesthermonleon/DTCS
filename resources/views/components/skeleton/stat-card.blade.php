{{--
    Skeleton: Statistics Card
    Usage: <x-skeleton.stat-card />
    Props:
      $color  = 'signal' | 'coral' | 'amber' | 'steel'  (left bar accent)
--}}
@props(['color' => 'signal'])

@php
$barColors = [
    'signal' => '#14C79A',
    'coral'  => '#E85C55',
    'amber'  => '#E0A030',
    'steel'  => '#4C7EA8',
];
$bar = $barColors[$color] ?? $barColors['signal'];
@endphp

<div {{ $attributes->merge(['class' => 'skeleton-stat-card', 'style' => "border-left-color: {$bar};"]) }}
     role="status" aria-label="Loading statistic" aria-busy="true">
    <div class="sk-stat-top">
        <div style="flex:1;">
            {{-- Large metric number --}}
            <div class="sk sk-3xl sk-w-25 mb-2"></div>
            {{-- Label --}}
            <div class="sk sk-sm sk-w-50"></div>
        </div>
        {{-- Icon box --}}
        <div class="sk sk-icon-lg" style="flex-shrink:0;"></div>
    </div>
    {{-- Sparkline --}}
    <div class="sk sk-sparkline"></div>
</div>
