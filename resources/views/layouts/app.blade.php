<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $branding?->nom ?? config('app.name') }} | {{ request()->routeIs('admin.*') ? 'Administration' : 'Mon espace' }}</title>
        <meta name="description" content="{{ $branding?->description ?? '' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased app-body">
        <div class="min-h-screen app-shell">
            @if (request()->routeIs('admin.*'))
                <div class="admin-shell">
                    @include('layouts.admin-sidebar')
                    <div class="admin-main">
                        @isset($header)
                            <header class="admin-topbar">{{ $header }}</header>
                        @endisset
                        <main>{{ $slot }}</main>
                    </div>
                </div>
            @elseif (auth()->check() && method_exists(auth()->user(), 'annonces'))
                <div class="admin-shell member-shell">
                    @include('layouts.member-sidebar')
                    <div class="admin-main">
                        @isset($header)
                            <header class="admin-topbar">{{ $header }}</header>
                        @endisset
                        <main>{{ $slot }}</main>
                    </div>
                </div>
            @else
                @include('layouts.navigation')
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
                <main>{{ $slot }}</main>
            @endif
        </div>
    </body>
</html>
