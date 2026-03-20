@extends('layouts.app')
@section('title','View Request')

@section('content')
    <x-page-header title="Resident Details" subtitle="View resident information">
        <x-slot:action>
            <a href="{{ route('certificates_request.index') }}" class="btn btn-light">Back</a>
        </x-slot:action>
    </x-page-header>

    <div class="row g-4">
        <div class="col-lg-12">
            <x-card>

                @if($resident)

                    <div class="mb-4">
                        <label class="form-label">Resident Details</label>
                        <div class="p-3 border rounded bg-light shadow-sm">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Full Name</small>
                                    <strong>{{ trim($resident->FirstName.' '.$resident->MiddleName.' '.$resident->LastName) }}</strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Gender</small>
                                    <div>{{ $resident->gender ?? '-' }}</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Civil Status</small>
                                    <div>{{ $resident->CivilStatus ?? '-' }}</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Birthdate</small>
                                    <div>{{ $resident->BirthDate ? \Carbon\Carbon::parse($resident->BirthDate)->format('M d, Y') : '-' }}</div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Age</small>
                                    <div>{{ $resident->BirthDate ? \Carbon\Carbon::parse($resident->BirthDate)->age : '-' }}</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ADDRESS --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <label class="form-label mb-0">Address Information</label>
                            <button class="btn btn-sm btn-primary" id="toggleAddressForm">
                                {{ $resident->info ? 'Edit Address' : '+ Add Address' }}
                            </button>
                        </div>

                        <div class="p-3 border rounded bg-light shadow-sm">

                            @if($resident->info)
                                <div class="mb-3">
                                    <small class="text-muted d-block">Full Address</small>
                                    <div>{{ $resident->info->full_address }}</div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('resident-info.store') }}"
                                  class="addr-form {{ $resident->info ? 'd-none' : '' }}">
                                @csrf

                                <input type="hidden" name="resident_id" value="{{ $resident->id ?? '' }}">

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label>Region</label>
                                        <select name="region" class="form-control select2 region">
                                            @if($resident->info?->region)
                                                <option value="{{ $resident->info->region }}" selected>
                                                    {{ $resident->info->regionRel->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Province</label>
                                        <select name="province" class="form-control select2 province">
                                            @if($resident->info?->province)
                                                <option value="{{ $resident->info->province }}" selected>
                                                    {{ $resident->info->provinceRel->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>City</label>
                                        <select name="city" class="form-control select2 city">
                                            @if($resident->info?->city)
                                                <option value="{{ $resident->info->city }}" selected>
                                                    {{ $resident->info->cityRel->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Barangay</label>
                                        <select name="barangay" class="form-control select2 barangay">
                                            @if($resident->info?->barangay)
                                                <option value="{{ $resident->info->barangay }}" selected>
                                                    {{ $resident->info->barangayRel->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Purok</label>
                                        <input type="text" name="purok"
                                               class="form-control purok"
                                               value="{{ $resident->info->purok ?? '' }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Street</label>
                                        <input type="text" name="street"
                                               class="form-control street"
                                               value="{{ $resident->info->street ?? '' }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Unit</label>
                                        <input type="text" name="unit"
                                               class="form-control unit"
                                               value="{{ $resident->info->unit ?? '' }}">
                                    </div>

                                    <div class="col-md-12">
                                        <label>Full Address</label>
                                        <input type="text" name="full_address"
                                               class="form-control full_address"
                                               value="{{ $resident->info->full_address ?? '' }}"
                                               readonly>
                                    </div>

                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100 mt-2">
                                            {{ $resident->info ? 'Update Address' : 'Save Address' }}
                                        </button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                    {{-- BUSINESS --}}
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-semibold">Business Information</h6>
                                <small class="text-muted">Manage registered businesses</small>
                            </div>
                            <button class="btn btn-success btn-sm px-3" id="toggleBusinessForm">
                                + Add
                            </button>
                        </div>

                        <div class="bg-white border rounded-3 shadow-sm p-3">

                            <form method="POST" class="biz-form d-none" action="{{ route('business.store') }}">
                                @csrf

                                <input type="hidden" name="resident_id" value="{{ $resident->id }}">

                                <div class="row g-3">

                                    <div class="col-md-3">
                                        <label class="form-label">Business Name</label>
                                        <input type="text" name="business_name" class="form-control" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">TIN No.</label>
                                        <input type="text" name="tin_no" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Operator Type</label>
                                        <select name="operator_type" id="operatorType" class="form-select" required>
                                            <option value="resident">Resident</option>
                                            <option value="custom">Manual Entry</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 operator-custom d-none">
                                        <label class="form-label">Operator Name</label>
                                        <input type="text" name="operator_name" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Region</label>
                                        <select name="region" class="form-control form-control-sm select2 region"></select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Province</label>
                                        <select name="province" class="form-control form-control-sm select2 province"></select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">City</label>
                                        <select name="city" class="form-control form-control-sm select2 city"></select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Barangay</label>
                                        <select name="barangay" class="form-control form-control-sm select2 barangay"></select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label small text-muted">Unit</label>
                                        <input type="text" name="unit" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Street</label>
                                        <input type="text" name="street" class="form-control">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label small text-muted">Purok</label>
                                        <input type="text" name="purok" class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">ZIP Code</label>
                                        <input type="text" name="zip" class="form-control">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label small text-muted">Full Address</label>
                                        <input type="text" name="full_address" class="full_address form-control" id="fullAddress">
                                    </div>


                                    <div class="col-12 text-end">
                                        <button class="btn btn-primary">Save Business</button>
                                    </div>

                                </div>
                            </form>

                            <hr class="my-3">

                            @forelse($resident->businesses as $biz)

                                @php
                                    $baddr = $biz->full_address;

                                    $operatorName = $biz->operator_type === 'resident'
                                        ? optional($biz->resident)->FirstName.' '.optional($biz->resident)->LastName
                                        : $biz->operator_name;
                                @endphp

                                <div class="border rounded-3 p-3 mb-2 bg-light-subtle">
                                    <div class="d-flex justify-content-between align-items-start">

                                        <div>
                                            <div class="fw-semibold">{{ $biz->business_name }}</div>
                                            <small class="text-muted">
                                                {{ $operatorName ?? '—' }} | {{ $biz->TinNo }}
                                            </small>
                                        </div>

                                    </div>

                                    <div class="mt-2 small text-muted">
                                        {{ $baddr }}
                                    </div>
                                </div>

                            @empty
                                <div class="text-muted text-center py-3">
                                    No business registered
                                </div>
                            @endforelse

                        </div>
                    </div>

                @endif

            </x-card>
        </div>
    </div>

@endsection
