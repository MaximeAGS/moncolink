<x-app-layout>
    <section class="admin-settings-page">
        <header class="admin-dashboard-heading">
            <div>
                <p class="admin-eyebrow">Identité du projet</p>
                <h1>Paramètres généraux</h1>
                <p>Le nom, la description, le slogan et le logo affichés sur le site.</p>
            </div>
        </header>

        @if (session('status'))
            <div class="admin-settings-success" role="status">{{ session('status') }}</div>
        @endif

        <form class="admin-settings-form" method="POST" action="{{ route('admin.parametres.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="admin-settings-field">
                <label for="nom">Nom du projet</label>
                <input id="nom" name="nom" type="text" value="{{ old('nom', $parametres->nom) }}" maxlength="100" required>
                @error('nom')<p class="admin-settings-error">{{ $message }}</p>@enderror
            </div>

            <div class="admin-settings-field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" maxlength="1000">{{ old('description', $parametres->description) }}</textarea>
                @error('description')<p class="admin-settings-error">{{ $message }}</p>@enderror
            </div>

            <div class="admin-settings-field">
                <label for="slogan">Slogan</label>
                <input id="slogan" name="slogan" type="text" value="{{ old('slogan', $parametres->slogan) }}" maxlength="160">
                <span class="admin-settings-hint">Phrase courte affichée dans les pieds de page et les espaces membre.</span>
                @error('slogan')<p class="admin-settings-error">{{ $message }}</p>@enderror
            </div>

            <div class="admin-settings-field">
                <label for="logo">Logo</label>
                @if ($parametres->logo)
                    <div class="admin-settings-logo-preview">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($parametres->logo) }}" alt="Logo actuel de {{ $parametres->nom }}">
                        <label class="admin-settings-remove-logo"><input type="checkbox" name="supprimer_logo" value="1"> Supprimer le logo actuel</label>
                    </div>
                @endif
                <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
                <span class="admin-settings-hint">JPG, PNG, WEBP ou GIF. Taille maximale : 2 Mo.</span>
                @error('logo')<p class="admin-settings-error">{{ $message }}</p>@enderror
            </div>

            <div class="admin-settings-actions">
                <button class="admin-primary-action" type="submit">Enregistrer les paramètres <span aria-hidden="true">↗</span></button>
            </div>
        </form>
    </section>
</x-app-layout>