<div class="page-header d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">{{ $title }}</h4>

        @if(isset($subtitle))
            <small class="text-muted">{{ $subtitle }}</small>
        @endif

    </div>

    @if(isset($action))
        <div>
            {{ $action }}
        </div>
    @endif

</div>
