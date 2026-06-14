@php
    $class = match($status) {
        'pending' => 'bg-yellow-lt text-yellow',
        'verified' => 'bg-green-lt text-green',
        'revision' => 'bg-orange-lt text-orange',
        'rejected' => 'bg-red-lt text-red',
        default => 'bg-secondary-lt text-secondary',
    };
@endphp
