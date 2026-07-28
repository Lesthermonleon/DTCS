{{--
    Skeleton: Generic Data Card (patient record, request card, etc.)
    Usage: <x-skeleton.card :count="3" />
    Props:
      $count = number of cards to render (default 1)
      $horizontal = side-by-side image+text layout (default false)
--}}
@props(['count' => 1, 'horizontal' => false])

@for ($i = 0; $i < $count; $i++)
<div class="skeleton-card mb-3" role="status" aria-label="Loading card" aria-busy="true">

    @if($horizontal)
        <div class="d-flex gap-3 align-items-start">
            {{-- Image / icon block --}}
            <div class="sk sk-icon-lg" style="width:64px;height:64px;border-radius:10px;flex-shrink:0;"></div>
            <div style="flex:1;">
                <div class="sk sk-lg sk-w-50 mb-2"></div>
                <div class="sk sk-md sk-w-75 mb-1"></div>
                <div class="sk sk-md sk-w-66 mb-1"></div>
                <div class="sk sk-md sk-w-33 mb-3"></div>
                <div class="d-flex gap-2">
                    <div class="sk sk-xl sk-pill" style="width:80px;"></div>
                    <div class="sk sk-xl sk-pill" style="width:64px;"></div>
                </div>
            </div>
        </div>
    @else
        {{-- Title --}}
        <div class="sk sk-lg sk-w-50 mb-2"></div>
        {{-- Subtitle / tag --}}
        <div class="d-flex gap-2 mb-3">
            <div class="sk sk-sm sk-pill" style="width:60px;"></div>
            <div class="sk sk-sm sk-pill" style="width:75px;"></div>
        </div>
        {{-- Body lines --}}
        <div class="sk sk-md sk-w-100 mb-1"></div>
        <div class="sk sk-md sk-w-75 mb-1"></div>
        <div class="sk sk-md sk-w-50 mb-3"></div>
        {{-- Action buttons --}}
        <div class="d-flex gap-2">
            <div class="sk sk-xl sk-pill" style="width:90px;"></div>
            <div class="sk sk-xl sk-pill" style="width:70px;"></div>
        </div>
    @endif

</div>
@endfor
