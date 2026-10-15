<x-guest-layout>
    <div class="auth-heading auth-heading-register">
        <p class="eyebrow"><span></span> Commencez ici</p>
        <h2>Créer mon compte</h2>
        <p>Quelques informations pour ouvrir votre espace {{ $branding?->nom ?? config('app.name') }}.</p>
    </div>

    <form class="auth-form register-form" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-form-grid">
            <div>
                <x-input-label for="prenom" value="Prénom" />
                <x-text-input id="prenom" class="auth-input mt-1 block w-full" type="text" name="prenom" :value="old('prenom')" required autofocus autocomplete="given-name" />
                <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="nom" value="Nom" />
                <x-text-input id="nom" class="auth-input mt-1 block w-full" type="text" name="nom" :value="old('nom')" required autocomplete="family-name" />
                <x-input-error :messages="$errors->get('nom')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="telephone" value="Téléphone (facultatif)" />
            <x-text-input id="telephone" class="auth-input mt-1 block w-full" type="tel" name="telephone" :value="old('telephone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="auth-input mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="auth-input mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" class="auth-input mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="auth-submit">Créer mon compte <span aria-hidden="true">↗</span></x-primary-button>
    </form>

    <p class="auth-switch">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>
</x-guest-layout>
