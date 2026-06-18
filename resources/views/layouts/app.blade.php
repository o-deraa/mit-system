<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MIT Management System' }}</title>

    {{-- Tabler UI CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Custom CSS project --}}
    <link rel="stylesheet" href="{{ asset('css/mit-custom.css') }}">

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
</head>

<body>
<div class="page">
    @if(session('mit_role'))
        @include('layouts.partials.sidebar')
    @endif

    <div class="page-wrapper">
        @if(session('mit_role'))
            @include('layouts.partials.topbar')
        @endif

        <div class="page-body">
            <div class="container-xl">
                @include('layouts.partials.flash')

                @yield('content')
            </div>
        </div>
    </div>
</div>

{{-- Tabler JS --}}
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('[data-confirm]');

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const message = form.getAttribute('data-confirm') || 'Yakin ingin melanjutkan?';

                if (!confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    });
</script>

@stack('scripts')
</body>
</html>
