{{--
    Skeleton: Form
    Usage: <x-skeleton.form :fields="5" :columns="1" />
    Props:
      $fields  = number of input fields (default 4)
      $columns = 1 or 2 column layout   (default 1)
      $button  = show submit button     (default true)
--}}
@props(['fields' => 4, 'columns' => 1, 'button' => true])

<div class="skeleton-card" role="status" aria-label="Loading form" aria-busy="true">

    @if($columns === 2)
        <div class="row g-4">
            @for ($i = 0; $i < $fields; $i++)
            <div class="col-md-6">
                <div class="skeleton-form-group">
                    <div class="sk sk-sm sk-w-33 sk-label"></div>
                    <div class="sk sk-2xl sk-w-100 mt-1" style="border-radius:8px;"></div>
                </div>
            </div>
            @endfor
        </div>
    @else
        @for ($i = 0; $i < $fields; $i++)
        <div class="skeleton-form-group">
            @php $w = [100, 130, 90, 120, 110][$i % 5]; @endphp
            <div class="sk sk-sm" {!! 'style="width:' . $w . 'px;"' !!}></div>
            @if ($i === 1 || $i === 3)
                {{-- Textarea --}}
                <div class="sk sk-w-100 mt-1" style="height:80px;border-radius:8px;"></div>
            @else
                <div class="sk sk-2xl sk-w-100 mt-1" style="border-radius:8px;"></div>
            @endif
        </div>
        @endfor
    @endif

    @if($button)
    <div class="d-flex gap-2 mt-2">
        <div class="sk sk-xl" style="width:100px;border-radius:8px;"></div>
        <div class="sk sk-xl" style="width:80px;border-radius:8px;"></div>
    </div>
    @endif

</div>
