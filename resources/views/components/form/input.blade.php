@props([
    'type' => 'text',
    'name',
    'value' => null,
    'placeholder' => null
])

<input
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $name }}"
    value="{{ old($name,$value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => 'form-control form-control-lg '.($errors->has($name) ? 'is-invalid' : '')
    ]) }}
>
