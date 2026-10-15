<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $branding?->nom ?? config('app.name') }} | Accès membre</title>
        <meta name="description" content="{{ $branding?->description ?? '' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-page">
        <main class="auth-shell">
            <section class="auth-visual" aria-label="Colocation et art de vivre">
                <x-brand-logo class="auth-wordmark" />
                <div class="auth-visual-copy">
                    <p class="eyebrow hero-eyebrow"><span></span> {{ $branding?->slogan ?: 'Un chez-vous, à plusieurs.' }}</p>
                    <h1>Le bon lieu.<br><em>Les bonnes personnes.</em></h1>
                    <p>{{ $branding?->description ?: 'Un espace pensé pour donner vie à votre prochain projet de colocation.' }}</p>
                </div>
                <span class="auth-photo-caption">Trouver sa place commence ici.</span>
            </section>

            <section class="auth-form-side">
                <x-brand-logo class="auth-mobile-wordmark" />
                <div class="auth-form-wrap">{{ $slot }}</div>
                <a class="auth-back-link" href="{{ url('/') }}">← Retour à l’accueil</a>
            </section>
        </main>
        <footer class="auth-footer"><span>© {{ date('Y') }} {{ config('app.name') }}</span><span>{{ $branding?->slogan ?: 'Un chez-vous, à plusieurs.' }}</span></footer>
    </body>
</html>
