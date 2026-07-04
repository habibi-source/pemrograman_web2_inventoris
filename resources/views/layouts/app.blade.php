<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LogisticsPro') &mdash; Inventory Control</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        @include('components.sidebar')

        <div class="content-area flex-grow-1 d-flex flex-column">
            @include('components.top-nav')

            <main class="flex-grow-1 p-4 overflow-auto" style="max-height: calc(100vh - 56px);">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
