@extends('layouts.app')
@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient: ' . $patient->patient_no)
@section('content')
<form method="POST" action="{{ route('patients.update', $patient) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2"></i>Personal Information</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $patient->last_name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $patient->first_name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $patient->middle_name) }}">
                    </div>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $patient->date_of_birth) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            @foreach(['Male','Female','Other'] as $g)
                                <option value="{{ $g }}" {{ ($patient->gender===$g)?'selected':'' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Blood Type</label>
                        <select name="blood_type" class="form-select">
                            <option value="">Unknown</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                <option value="{{ $bt }}" {{ ($patient->blood_type===$bt)?'selected':'' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-2"><label class="form-label fw-semibold">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $patient->address) }}</textarea>
                </div>
                <div class="row g-2 mt-1">
                    <div class="col-md-6"><label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}">
                    </div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $patient->email) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-hospital me-2"></i>Admission</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Patient Type <span class="text-danger">*</span></label>
                        <select name="patient_type" class="form-select" required>
                            @foreach(['Inpatient','Outpatient'] as $pt)
                                <option value="{{ $pt }}" {{ ($patient->patient_type===$pt)?'selected':'' }}>{{ $pt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ward</label>
                        <select name="ward" class="form-select">
                            <option value="">—</option>
                            @foreach(['Ward A','Ward B','ICU','Pediatrics','OB-Gyne','Surgical Ward','Medical Ward'] as $w)
                                <option value="{{ $w }}" {{ ($patient->ward===$w)?'selected':'' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bed Number</label>
                        <input type="text" name="bed_number" class="form-control" value="{{ old('bed_number', $patient->bed_number) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>Emergency Contact</div>
            <div class="card-body">
                <div class="mb-2"><label class="form-label fw-semibold">Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
                </div>
                <div><label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt-3 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Update Patient</button>
    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
@endsection
