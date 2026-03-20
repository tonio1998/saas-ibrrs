@extends('layouts.app')
@section('title','Certificate')

@section('content')

    @php
        $certificate = $certificate ?? null;
        $isEdit = !is_null($certificate);
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-6">
            <x-page-header
                title="{{ $isEdit ? 'Edit Certificate' : 'Issue Certificate' }}"
                subtitle="Certificate management"
            >
                <x-slot:action>
                    <a href="{{ route('certificates_request.index') }}" class="btn btn-light">
                        Back
                    </a>
                </x-slot:action>
            </x-page-header>

            <x-card>
                <form method="POST"
                      action="{{ $isEdit
                    ? route('certificates_request.update', encrypt($certificate->id))
                    : route('certificates_request.store') }}">

                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    <div class="row g-4">
                        <x-form.group name="resident_id" label="Resident" class="col-md-12">
                            <x-form.select
                                name="resident_id"
                                id="resident_id"
                                ajax="{{ route('select2.residents') }}"
                                value="{{ old('resident_id', $certificate?->resident_id) }}"
                                text="{{ $certificate && $certificate->resident
                                    ? $certificate->resident->FirstName.' '.$certificate->resident->LastName
                                    : '' }}"
                                placeholder="Select resident"
                            />
                        </x-form.group>

                        <x-form.group name="certificate_type_id" label="Document Type" class="col-md-12">
                            <x-form.select
                                name="certificate_type_id"
                                id="certificate_type_id"
                                ajax="{{ route('select2.certificate-types') }}"
                                value="{{ old('certificate_type_id', $certificate?->certificate_type_id) }}"
                                text="{{ $certificate && $certificate->certificateType
                                    ? $certificate->certificateType->name
                                    : '' }}"
                                placeholder="Select Document Type"
                            />
                        </x-form.group>

                        <div class="business-field d-none">
                            <x-form.group name="business_id" label="Business" class="col-md-12">
{{--                                <x-form.select--}}
{{--                                    name="business_id"--}}
{{--                                    ajax="{{ route('select2.businesses') }}"--}}
{{--                                    value="{{ old('business_id') }}"--}}
{{--                                    text=""--}}
{{--                                    placeholder="Select Business"--}}
{{--                                />--}}
                                <select name="business_id" class="form-select get-resident-business"></select>
                            </x-form.group>
                        </div>

                        <x-form.group name="purpose" label="Purpose" class="col-md-12">
                            <x-form.input
                                name="purpose"
                                value="{{ old('purpose', $certificate?->purpose) }}"
                            />
                        </x-form.group>

                        <x-form.group name="remark" label="Remark" class="col-md-12">
                            <x-form.input
                                name="remark"
                                value="{{ old('remark', $certificate?->remark) }}"
                            />
                        </x-form.group>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary">
                            {{ $isEdit ? 'Update' : 'Issue Certificate' }}
                        </button>
                    </div>

                </form>
            </x-card>
        </div>
    </div>
@endsection
