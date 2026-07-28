{{--
    Skeleton: Modal Window Content
    Usage: Inside a Bootstrap modal body: <x-skeleton.modal />
    Props:
      $type = 'form' | 'detail' | 'confirm'  (default 'form')
--}}
@props(['type' => 'form'])

<div class="skeleton-modal-body" role="status" aria-label="Loading modal content" aria-busy="true">

    @if($type === 'confirm')
        {{-- Confirmation dialog --}}
        <div class="text-center py-2">
            <div class="sk sk-avatar-xl mx-auto mb-3" style="width:56px;height:56px;border-radius:50%;"></div>
            <div class="sk sk-lg sk-w-50 mx-auto mb-2"></div>
            <div class="sk sk-md sk-w-75 mx-auto mb-1"></div>
            <div class="sk sk-md sk-w-66 mx-auto"></div>
        </div>

    @elseif($type === 'detail')
        {{-- Detail / read-only view --}}
        @for ($i = 0; $i < 5; $i++)
        <div class="d-flex gap-3 py-2" style="border-bottom:1px solid #F0EDE6;">
            <div class="sk sk-md" style="width:120px;flex-shrink:0;"></div>
            <div class="sk sk-md" style="flex:1;"></div>
        </div>
        @endfor

    @else
        {{-- Form (default) --}}
        @for ($i = 0; $i < 4; $i++)
        <div class="skeleton-form-group">
            @php $w = [100, 130, 90, 120][$i]; @endphp
            <div class="sk sk-sm" {!! 'style="width:' . $w . 'px;"' !!}></div>
            <div class="sk sk-2xl sk-w-100 mt-1" style="border-radius:8px;"></div>
        </div>
        @endfor
    @endif

</div>

{{-- Modal footer --}}
<div class="d-flex justify-content-end gap-2 mt-3 pt-3" style="border-top:1px solid #E6E2D6;">
    <div class="sk sk-xl" style="width:80px;border-radius:8px;"></div>
    <div class="sk sk-xl" style="width:90px;border-radius:8px;"></div>
</div>
