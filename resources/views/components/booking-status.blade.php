@php
    $class = match($status) {
        'pending' => 'bg-yellow-lt text-yellow',
        'accepted' => 'bg-blue-lt text-blue',
        'cancelled' => 'bg-red-lt text-red',
        'completed' => 'bg-green-lt text-green',
        default => 'bg-secondary-lt text-secondary',
    };
@endphp

<span class="badge {{ $class }}">
    {{ $status }}
</span>
