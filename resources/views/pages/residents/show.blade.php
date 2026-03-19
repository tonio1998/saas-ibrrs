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

                    {{-- BASIC INFO --}}
                    <div class="mb-4">
                        <label class="form-label">Resident Details</label>
                        <div class="p-3 border rounded bg-light shadow-sm">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Full Name</small>
                                    <strong>
                                        {{ trim($resident->FirstName.' '.$resident->MiddleName.' '.$resident->LastName) }}
                                    </strong>
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
                                    <div>
                                        {{ $resident->BirthDate ? \Carbon\Carbon::parse($resident->BirthDate)->format('M d, Y') : '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">Age</small>
                                    <div>
                                        {{ $resident->BirthDate ? \Carbon\Carbon::parse($resident->BirthDate)->age : '-' }}
                                    </div>
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
                            @else
                                <div class="text-muted mb-3">No address information</div>
                            @endif


                            {{-- FORM --}}
                            <form method="POST" action="{{ route('resident-info.store') }}" id="addressForm" class="{{ $resident->info ? 'd-none' : '' }}">
                                @csrf
                                <input type="hidden" name="resident_id" value="{{ $resident->id ?? '' }}">

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label>Region</label>
                                        <select name="region" id="aregion" class="form-control select2" data-ajax="/select2/address/regions"></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Province</label>
                                        <select name="province" id="aprovince" class="form-control select2" data-ajax="/select2/address/provinces"></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>City / Municipality</label>
                                        <select name="city" id="acity" class="form-control select2" data-ajax="/select2/address/cities"></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Barangay</label>
                                        <select name="barangay" id="abarangay" class="form-control select2" data-ajax="/select2/address/barangays"></select>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Purok</label>
                                        <input type="text" name="purok" id="purok" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Street</label>
                                        <input type="text" name="street" id="street" class="form-control">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Unit / House No.</label>
                                        <input type="text" name="unit" id="unit" class="form-control">
                                    </div>

                                    <div class="col-md-12">
                                        <label>Full Address (Auto)</label>
                                        <input type="text" id="full_address" name="full_address" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100 mt-2">Save Address</button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>


                    {{-- BUSINESS --}}
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <label class="form-label mb-0">Business Information</label>
                            <button class="btn btn-sm btn-success">+ Add Business</button>
                        </div>

                        <div class="p-3 border rounded bg-light shadow-sm">
                            @forelse($resident->businesses as $biz)

                                @php
                                    $baddr = collect([
                                        $biz->unit,
                                        $biz->street,
                                        $biz->purok ? 'Purok '.$biz->purok : null,
                                        $biz->barangay,
                                        $biz->city,
                                        $biz->province,
                                        $biz->region,
                                        $biz->zip
                                    ])->filter()->implode(', ');
                                @endphp

                                <div class="border rounded p-3 mb-3 bg-white">
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Business Name</small>
                                            <strong>{{ $biz->business_name }}</strong>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Operator ID</small>
                                            <div>{{ $biz->operator_id }}</div>
                                        </div>

                                        <div class="col-md-12">
                                            <small class="text-muted d-block">Business Address</small>
                                            <div>{{ $baddr }}</div>
                                        </div>

                                    </div>
                                </div>

                            @empty
                                <div class="text-muted">No business registered</div>
                            @endforelse
                        </div>
                    </div>

                @else
                    <div class="form-control bg-light">No resident found</div>
                @endif

            </x-card>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const toggleBtn = document.getElementById('toggleAddressForm')
            const form = document.getElementById('addressForm')

            if (toggleBtn && form) {
                toggleBtn.addEventListener('click', () => {
                    form.classList.toggle('d-none')
                })
            }

            const buildAddress = () => {
                const unit = document.getElementById('unit')?.value || ''
                const street = document.getElementById('street')?.value || ''
                const purok = document.getElementById('purok')?.value ? 'Purok ' + document.getElementById('purok').value : ''

                const barangay = $('#abarangay').find(':selected').text() || ''
                const city = $('#acity').find(':selected').text() || ''
                const province = $('#aprovince').find(':selected').text() || ''
                const region = $('#aregion').find(':selected').text() || ''

                const full = [unit, street, purok, barangay, city, province, region]
                    .filter(v => v && v !== 'Select option')
                    .join(', ')

                document.getElementById('full_address').value = full
            }

            $('#aregion, #aprovince, #acity, #abarangay').on('change', buildAddress)
            $('#unit, #street, #purok').on('input', buildAddress)

        })
    </script>
@endsection
