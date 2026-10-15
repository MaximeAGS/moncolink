<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5f6f1">
        <title>@yield('title', ($branding?->nom ?? config('app.name')).' | La colocation, simplement')</title>
        <meta name="description" content="{{ $branding?->description ?: 'Trouvez un lieu où vous vous sentez chez vous, et des personnes avec qui le partager.' }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="welcome-page">
        <header class="welcome-nav">
            <x-brand-logo :label="($branding?->nom ?? config('app.name')).', accueil'" />
            <nav class="welcome-nav-links" aria-label="Navigation principale">
                <a href="{{ url('/') }}#notre-approche">Notre approche</a>
                @auth
                    @if (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('administrateur'))
                        <a class="nav-action" href="{{ route('admin.dashboard') }}">Administration <span aria-hidden="true">↗</span></a>
                    @else
                        <a class="nav-action" href="{{ route('dashboard') }}">Mon espace <span aria-hidden="true">↗</span></a>
                    @endif
                @else
                    <a href="{{ route('login') }}">Connexion</a>
                    @if (Route::has('register'))
                        <a class="nav-action" href="{{ route('register') }}">Créer un compte <span aria-hidden="true">↗</span></a>
                    @endif
                @endauth
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="welcome-footer">
            <x-brand-logo />
            <p>{{ $branding?->slogan ?: 'Un chez-vous, à plusieurs.' }}</p>
            <nav aria-label="Liens de pied de page">
                <a href="{{ route('login') }}">Connexion</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Inscription</a>
                @endif
            </nav>
            <span>© {{ date('Y') }} {{ config('app.name') }}</span>
        </footer>
    </body>
</html>