<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Informations du profil</h2>

        <p class="mt-1 text-sm text-gray-600">
            Modifiez vos coordonnées et les informations visibles par les autres membres.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @if ($user instanceof \App\Models\Utilisateur)
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="prenom" value="Prénom" />
                    <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" :value="old('prenom', $user->prenom)" required autocomplete="given-name" />
                    <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
                </div>
                <div>
                    <x-input-label for="nom" value="Nom" />
                    <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $user->nom)" required autocomplete="family-name" />
                    <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="telephone" value="Téléphone" />
                <x-text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full" :value="old('telephone', $user->telephone)" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
            </div>
        @else
            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>
        @endif

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Votre adresse e-mail n’est pas vérifiée.

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Renvoyer le courriel de vérification
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Un nouveau lien de vérification vous a été envoyé.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Modifications enregistrées.</p>
            @endif
        </div>
    </form>
</section>
