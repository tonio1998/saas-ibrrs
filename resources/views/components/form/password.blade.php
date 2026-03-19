@props([
'name',
'placeholder' => ''
])

<div class="position-relative">

    <input
        type="password"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control pe-5 password-field']) }}
    >

    <i class="bi bi-eye password-toggle"></i>

</div>
