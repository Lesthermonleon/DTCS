{{--
    Skeleton: Data Table
    Usage: <x-skeleton.table :rows="8" :cols="5" />
    Props:
      $rows = number of skeleton rows  (default 6)
      $cols = number of columns        (default 4)
      $searchBar = show search bar above table (default true)
      $pagination = show pagination below (default true)
--}}
@props(['rows' => 6, 'cols' => 4, 'searchBar' => true, 'pagination' => true])

<div role="status" aria-label="Loading table data" aria-busy="true">

    {{-- Search + action bar --}}
    @if($searchBar)
    <div class="d-flex gap-2 mb-3 align-items-center">
        <div class="sk sk-lg" style="width:220px;border-radius:8px;"></div>
        <div class="sk sk-lg" style="width:100px;border-radius:8px;margin-left:auto;"></div>
        <div class="sk sk-lg" style="width:80px;border-radius:8px;"></div>
    </div>
    @endif

    <div class="skeleton-card p-0 overflow-hidden">
        {{-- Table header --}}
        <div class="skeleton-table-header">
            @for ($c = 0; $c < $cols; $c++)
                @php $w = [60,90,80,55,45][$c % 5]; @endphp
                <div class="sk sk-xs sk-pill" {!! 'style="width:' . $w . 'px;"' !!}></div>
            @endfor
        </div>

        {{-- Table rows --}}
        @for ($r = 0; $r < $rows; $r++)
        <div class="skeleton-table-row">
            @for ($c = 0; $c < $cols; $c++)
                @php
                    $widths = ['50', '75', '66', '25', '33'];
                    $w = $widths[$c % count($widths)];
                @endphp
                <div class="sk sk-md sk-w-{{ $w }}" style="flex:1;"></div>
            @endfor
        </div>
        @endfor
    </div>

    {{-- Pagination --}}
    @if($pagination)
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="sk sk-sm" style="width:120px;"></div>
        <div class="d-flex gap-2">
            @for ($p = 0; $p < 5; $p++)
                <div class="sk sk-circle" style="width:32px;height:32px;"></div>
            @endfor
        </div>
    </div>
    @endif

</div>
