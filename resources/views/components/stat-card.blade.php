<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            @isset($icon)
                <span class="avatar me-3">
                    <i class="{{ $icon }}"></i>
                </span>
            @endisset

            <div>
                <div class="text-muted mit-text-small">{{ $label }}</div>
                <div class="h2 mb-0">{{ $value }}</div>
            </div>
        </div>
    </div>
</div>
