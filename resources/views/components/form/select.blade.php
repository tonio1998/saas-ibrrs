@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'text' => null,
    'placeholder' => null,
    'ajax' => null,
    'inline' => false,
    'style' => false,
    'class' => ''
])

@php
    $selectedValue = old($name, $value);
    $selectedText = $text ?? '';
@endphp

@php
    $select = '
        <select
            name="'.$name.'"
            data-ajax="'.$ajax.'"
            data-placeholder="'.$placeholder.'"
            data-value="'.$selectedValue.'"
            data-selected="'.$selectedText.'"
            '.$attributes->merge(['class'=>'form-select select2']).'
        >

            <option></option>';

    if ($ajax) {

        if ($selectedValue && $selectedText) {
            $select .= '<option value="'.$selectedValue.'" selected>'.$selectedText.'</option>';
        }

    } else {

        foreach ($options as $key => $optionText) {
            $isSelected = $selectedValue == $key ? 'selected' : '';
            $select .= '<option value="'.$key.'" '.$isSelected.'>'.$optionText.'</option>';
        }

    }

    $select .= '</select>';
@endphp


@if($inline)

    <div class="row mb-3 align-items-center {{ $class }}" {{ $style }}>

        @if($label)
            <label class="col-sm-3 col-form-label fw-semibold">
                {{ $label }}
            </label>
        @endif

        <div class="col-sm-9">
            {!! $select !!}
        </div>

    </div>

@else

    <div class="mb-3 {{ $class }}" {{ $style }}>

        @if($label)
            <label class="form-label fw-semibold">
                {{ $label }}
            </label>
        @endif

        {!! $select !!}

    </div>

@endif
