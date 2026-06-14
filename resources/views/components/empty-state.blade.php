<div class="mit-empty-state">
    <i class="{{ $icon ?? 'ti ti-inbox' }}"></i>
    <div class="fw-semibold">{{ $title ?? 'Data kosong' }}</div>

    @isset($description)
        <div class="text-muted mt-1">{{ $description }}</div>
    @endisset
</div>
