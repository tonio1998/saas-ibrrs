@props(['status'])

@php
    $status = strtolower($status ?? 'pending');

    $map = [
        'pending' => ['class' => 'secondary', 'label' => 'Pending'],
        'processing' => ['class' => 'warning', 'label' => 'Processing'],
        'approved' => ['class' => 'success', 'label' => 'Approved'],
        'rejected' => ['class' => 'danger', 'label' => 'Rejected'],
    ];

    $current = $map[$status] ?? $map['pending'];
@endphp

<span class="badge bg-{{ $current['class'] }}">
    {{ $current['label'] }}
</span>
