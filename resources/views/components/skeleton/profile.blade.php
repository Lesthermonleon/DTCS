{{--
    Skeleton: User Profile Page
    Usage: <x-skeleton.profile />
    Props:
      $tabs = show profile tabs below header (default true)
--}}
@props(['tabs' => true])

<div role="status" aria-label="Loading profile" aria-busy="true">

    {{-- Profile header --}}
    <div class="skeleton-card mb-3">
        <div class="skeleton-profile-header">
            {{-- Avatar --}}
            <div class="sk sk-avatar-2xl"></div>
            {{-- Info --}}
            <div style="flex:1;">
                <div class="sk sk-xl sk-w-33 mb-2"></div>
                <div class="sk sk-md sk-w-25 mb-2"></div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="sk sk-sm sk-pill" style="width:90px;"></div>
                    <div class="sk sk-sm sk-pill" style="width:110px;"></div>
                    <div class="sk sk-sm sk-pill" style="width:80px;"></div>
                </div>
            </div>
            {{-- Edit button --}}
            <div class="sk sk-xl sk-rounded sk-hide-mobile" style="width:100px;"></div>
        </div>
    </div>

    {{-- Tabs --}}
    @if($tabs)
    <div class="d-flex gap-3 mb-3">
        @for ($t = 0; $t < 4; $t++)
            @php $w = [80,100,90,70][$t]; @endphp
            <div class="sk sk-lg sk-rounded" {!! 'style="width:' . $w . 'px;"' !!}></div>
        @endfor
    </div>
    @endif

    {{-- Profile form skeleton --}}
    <div class="skeleton-card">
        <div class="row g-4">
            @for ($i = 0; $i < 6; $i++)
            <div class="col-md-6">
                <div class="skeleton-form-group">
                    <div class="sk sk-sm sk-w-33 sk-label"></div>
                    <div class="sk sk-2xl sk-w-100 mt-1" style="border-radius:8px;"></div>
                </div>
            </div>
            @endfor
        </div>
        <div class="d-flex gap-2 mt-2">
            <div class="sk sk-xl" style="width:120px;border-radius:8px;"></div>
        </div>
    </div>

</div>
