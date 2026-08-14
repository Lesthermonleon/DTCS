{{-- ── Report Filters Partial ── --}}
<form method="GET" action="{{ url()->current() }}" class="card mb-4 print-hide" id="reportFilters">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            {{-- Date From --}}
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold small mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from', $from->format('Y-m-d')) }}">
            </div>
            {{-- Date To --}}
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold small mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to', $to->format('Y-m-d')) }}">
            </div>
            {{-- Status (optional) --}}
            @if(!empty($statuses))
            <div class="col-md-2 col-sm-6">
                <label class="form-label fw-semibold small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            {{-- Doctor (optional) --}}
            @if(!empty($doctors))
            <div class="col-md-2 col-sm-6">
                <label class="form-label fw-semibold small mb-1">Doctor</label>
                <select name="doctor_id" class="form-select form-select-sm">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $d)
                        <option value="{{ $d->id }}" {{ request('doctor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            {{-- Extra Filters Slot --}}
            @if(!empty($extraFilters))
                {!! $extraFilters !!}
            @endif
            {{-- Submit --}}
            <div class="col-md-2 col-sm-6">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i>Generate Report
                </button>
            </div>
        </div>
    </div>
</form>
