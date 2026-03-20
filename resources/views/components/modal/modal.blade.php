@props([
    'id',
    'title' => '',
    'size' => 'sm',
    'centered' => true
])

<div class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-{{ $size }} {{ $centered ? 'modal-dialog-centered' : '' }}">
        <div class="modal-content ios-modal-premium">

            @if($title || isset($header))
                <div class="modal-header border-0 pb-0 cursor-move" data-drag>
                    @isset($header)
                        {{ $header }}
                    @else
                        <h6 class="modal-title fw-semibold">{{ $title }}</h6>
                    @endisset
                    <button type="button" class="btn-close small" data-bs-dismiss="modal"></button>
                </div>
            @endif

            <div class="modal-body pt-2">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="modal-footer border-0 pt-2">
                    {{ $footer }}
                </div>
            @endif

        </div>
    </div>
</div>
