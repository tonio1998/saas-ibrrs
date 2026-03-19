@extends('layouts.app')
@section('title','Certificate Type')

@section('content')

    @php
        $type = $type ?? null;
        $isEdit = !is_null($type);
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6">

            <x-page-header
                title="{{ $isEdit ? 'Edit Certificate Type' : 'Create Certificate Type' }}"
                subtitle="Manage certificate document types"
            >
                <x-slot:action>
                    <a href="{{ route('certificate-types.index') }}" class="btn btn-light btn-sm">
                        Back
                    </a>
                </x-slot:action>
            </x-page-header>

            <x-card>
                <form method="POST"
                      action="{{ $isEdit
                    ? route('certificate-types.update', encrypt($type->id))
                    : route('certificate-types.store') }}"
                      autocomplete="off">

                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    <div class="row g-3">

                        {{-- Name --}}
                        <x-form.group name="name" label="Certificate Name *" class="col-12">
                            <x-form.input
                                name="name"
                                value="{{ old('name', $type?->name) }}"
                                placeholder="e.g. Barangay Clearance"
                            />
                        </x-form.group>

                        {{-- Description --}}
                        <x-form.group name="description" label="Description" class="col-12">
                            <x-form.input
                                name="description"
                                value="{{ old('description', $type?->description) }}"
                                placeholder="Short description (optional)"
                            />
                        </x-form.group>

                        {{-- Template --}}
                        <x-form.group name="template" label="Template" class="col-12">
                            <x-form.input
                                name="template"
                                value="{{ old('template', $type?->template) }}"
                                placeholder="Template reference / filename"
                            />
                        </x-form.group>

                        {{-- Fee --}}
                        <x-form.group name="fee" label="Fee *" class="col-12">
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <x-form.input
                                    name="fee"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('fee', $type?->fee ?? 0) }}"
                                    placeholder="0.00"
                                />
                            </div>
                        </x-form.group>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('certificate-types.index') }}" class="btn btn-light btn-sm">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary btn-sm">
                            {{ $isEdit ? 'Update' : 'Save Certificate Type' }}
                        </button>
                    </div>

                </form>
            </x-card>

        </div>
    </div>

@endsection
