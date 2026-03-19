@props(['label', 'value'])

<div>
    <label class="text-muted small">{{ $label }}</label>
    <div class="fw-semibold">
        {{ $value ?? '-' }}
    </div>
</div>
