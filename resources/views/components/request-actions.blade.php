@props(['status', 'id'])

@php
    $status = strtolower($status ?? 'pending');
    $eid = encrypt($id);
@endphp

<div class="d-grid gap-2">

    @if(in_array($status, ['pending','processing']))
        <form action="{{ route('certificates_request.approve', $eid) }}" method="POST">
            @csrf
            @method('PUT')
            <button class="btn btn-success w-100">Approve</button>
        </form>

        <form action="{{ route('certificates_request.reject', $eid) }}" method="POST">
            @csrf
            @method('PUT')
            <button class="btn btn-danger w-100">Reject</button>
        </form>
    @endif

    @if($status === 'approved')
        <a href="{{ route('certificates_request.print', $eid) }}" class="btn btn-primary">
            Print Certificate
        </a>
    @endif

    @if($status === 'rejected')
        <button class="btn btn-secondary" disabled>
            Rejected
        </button>
    @endif

</div>
