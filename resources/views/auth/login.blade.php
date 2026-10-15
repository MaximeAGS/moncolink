<x-guest-layout>
    <div class="auth-heading">
        <p class="eyebrow"><span></span> Heureux de vous revoir</p>
        <h2>Connexion</h2>
        <p>Retrouvez votre espace et la suite de votre projet.</p>
    </div>

    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form class="auth-form" method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="auth-input mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="auth-input mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-options">
            <label for="remember_me" class="auth-check">
                <input id="remember_me" type="checkbox" name="remember" value="1">
                <span>Se souvenir de moi</span>
            </label>
            @if (Route::has('password.request'))
                <a class="auth-inline-link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>

        <x-primary-button class="auth-submit">Se connecter <span aria-hidden="true">↗</span></x-primary-button>
    </form>

    <p class="auth-switch">Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a></p>
</x-guest-layout>
