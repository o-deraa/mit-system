<div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">{{ $title }}</h2>

            @isset($subtitle)
                <div class="text-muted mt-1">{{ $subtitle }}</div>
            @endisset
        </div>

        @isset($actions)
            <div class="col-auto ms-auto d-print-none">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
