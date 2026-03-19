@extends('layouts.app')
@section('title','Verification')

@section('content')

    <div class="container py-5 text-center">

        @if($status === 'invalid')
            <div class="alert alert-danger">
                Invalid Certificate
            </div>
        @else

            <div class="card p-4">

                <h4 class="mb-3">Certificate Verified</h4>

                <p><strong>Control No:</strong> {{ $certificate->ControlNo }}</p>

                <p><strong>Resident:</strong>
                    {{ $certificate->resident->FirstName }} {{ $certificate->resident->LastName }}
                </p>

                <p><strong>Type:</strong> {{ $certificate->certificateType->name }}</p>

                <p>
                    <strong>Status:</strong>
                    <span class="badge bg-{{ $certificate->status === 'Approved' ? 'success':'warning' }}">
                    {{ $certificate->status }}
                </span>
                </p>

            </div>

        @endif

    </div>

@endsection
