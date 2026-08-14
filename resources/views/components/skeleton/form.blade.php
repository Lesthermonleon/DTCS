{{--
    Skeleton: Form Card
    Usage: <x-skeleton.form :fields="6" :columns="2" />
    Props:
      $fields  = number of input fields (default 6)
      $columns = 1 or 2 column layout   (default 2)
      $button  = show submit button     (default true)
--}}
@props(['fields' => 6, 'columns' => 2, 'button' => true])

<div role="status" aria-label="Loading form" aria-busy="true">

    <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
        
        {{-- Card Header --}}
        <div class="card-header bg-white py-3 border-bottom">
            <div class="sk sk-md mb-1" style="width:180px;border-radius:4px;"></div>
            <div class="sk sk-xs" style="width:260px;border-radius:4px;"></div>
        </div>

        {{-- Card Body --}}
        <div class="card-body p-4">
            @if($columns === 2)
                <div class="row g-4">
                    @for ($i = 0; $i < $fields; $i++)
                    <div class="col-md-6">
                        <div class="skeleton-form-group">
                            <div class="sk sk-xs mb-1.5" style="width:110px;border-radius:4px;"></div>
                            <div class="sk sk-xl w-100" style="height:38px;border-radius:6px;"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @for ($i = 0; $i < $fields; $i++)
                    <div class="skeleton-form-group">
                        <div class="sk sk-xs mb-1.5" style="width:120px;border-radius:4px;"></div>
                        @if ($i === 1)
                            <div class="sk sk-xl w-100" style="height:80px;border-radius:6px;"></div>
                        @else
                            <div class="sk sk-xl w-100" style="height:38px;border-radius:6px;"></div>
                        @endif
                    </div>
                    @endfor
                </div>
            @endif
        </div>

        {{-- Card Footer Buttons --}}
        @if($button)
        <div class="card-footer bg-white py-3 d-flex gap-2 border-top">
            <div class="sk sk-md" style="width:110px;height:36px;border-radius:6px;"></div>
            <div class="sk sk-md" style="width:80px;height:36px;border-radius:6px;"></div>
        </div>
        @endif

    </div>

</div>
