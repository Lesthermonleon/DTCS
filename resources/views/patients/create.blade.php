@extends('layouts.app')
@section('title', 'New Patient')
@section('page-title', 'Register New Patient')
@section('content')
<form method="POST" action="{{ route('patients.store') }}">
@csrf
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2"></i>Personal Information</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select</option>
                            @foreach(['Male','Female','Other'] as $g)
                                <option value="{{ $g }}" {{ old('gender')===$g?'selected':'' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Blood Type</label>
                        <select name="blood_type" class="form-select">
                            <option value="">Unknown</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                <option value="{{ $bt }}" {{ old('blood_type')===$bt?'selected':'' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="form-label fw-semibold">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-hospital me-2"></i>Admission Details</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Patient Type <span class="text-danger">*</span></label>
                        <select name="patient_type" class="form-select @error('patient_type') is-invalid @enderror" required id="patientType">
                            @foreach(['Inpatient','Outpatient'] as $pt)
                                <option value="{{ $pt }}" {{ old('patient_type')===$pt?'selected':'' }}>{{ $pt }}</option>
                            @endforeach
                        </select>
                        @error('patient_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ward <small class="text-muted">(if Inpatient)</small></label>
                        <select name="ward" class="form-select">
                            <option value="">—</option>
                            @foreach(['Ward A','Ward B','ICU','Pediatrics','OB-Gyne','Surgical Ward','Medical Ward'] as $w)
                                <option value="{{ $w }}" {{ old('ward')===$w?'selected':'' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bed Number</label>
                        <input type="text" name="bed_number" class="form-control" value="{{ old('bed_number') }}" placeholder="BED-001">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>Emergency Contact</div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                </div>
                <div>
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}" placeholder="09XXXXXXXXX">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt-3 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Register Patient</button>
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
@endsection
