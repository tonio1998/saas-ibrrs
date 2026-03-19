@props([
'name',
'label'=>null,
'class'=>'',
'required'=>false
])

<div class="{{ $class }}">

    @if($label)
        <label class="form-label fw-bold">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @error($name)
    <div class="text-danger small mt-1">
        {{ $message }}
    </div>
    @enderror

</div>
