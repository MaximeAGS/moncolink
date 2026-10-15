<x-app-layout>
    <section class="member-form-page">
        <header class="member-form-heading">
            <a class="member-back-link" href="{{ route('dashboard') }}">← Retour au tableau de bord</a>
            <p class="admin-eyebrow">Votre espace membre</p>
            <h1>{{ $annonce->exists ? 'Modifier mon annonce' : 'Publier une annonce' }}</h1>
            <p>{{ $annonce->exists ? 'Toute modification repassera en validation avant publication.' : 'Décrivez votre offre ou votre recherche de colocation.' }}</p>
        </header>

        <form class="member-annonce-form" method="POST" action="{{ $annonce->exists ? route('mes-annonces.update', $annonce) : route('mes-annonces.store') }}">
            @csrf
            @if ($annonce->exists)
                @method('PUT')
            @endif

            <div class="member-form-grid">
                <label class="member-form-field">
                    <span>Type d’annonce *</span>
                    <select name="type" required>
                        <option value="offre" @selected(old('type', $annonce->type) === 'offre')>Je propose une chambre</option>
                        <option value="recherche" @selected(old('type', $annonce->type) === 'recherche')>Je recherche une colocation</option>
                    </select>
                    @error('type')<small>{{ $message }}</small>@enderror
                </label>
                <label class="member-form-field">
                    <span>Titre *</span>
                    <input name="titre" type="text" maxlength="255" value="{{ old('titre', $annonce->titre) }}" required>
                    @error('titre')<small>{{ $message }}</small>@enderror
                </label>
            </div>

            <label class="member-form-field">
                <span>Description *</span>
                <textarea name="description" rows="5" maxlength="5000" required>{{ old('description', $annonce->description) }}</textarea>
                @error('description')<small>{{ $message }}</small>@enderror
            </label>

            <div class="member-form-grid member-form-grid-three">
                <label class="member-form-field">
                    <span>Ville</span>
                    <select name="ville_id">
                        <option value="">Choisir une ville</option>
                        @foreach ($villes as $ville)
                            <option value="{{ $ville->id }}" @selected((string) old('ville_id', $annonce->ville_id) === (string) $ville->id)>{{ $ville->nom }}</option>
                        @endforeach
                    </select>
                    @error('ville_id')<small>{{ $message }}</small>@enderror
                </label>
                <label class="member-form-field">
                    <span>Commune</span>
                    <select name="commune_id">
                        <option value="">Choisir une commune</option>
                        @foreach ($communes as $commune)
                            <option value="{{ $commune->id }}" @selected((string) old('commune_id', $annonce->commune_id) === (string) $commune->id)>{{ $commune->nom }} · {{ $commune->ville?->nom }}</option>
                        @endforeach
                    </select>
                    @error('commune_id')<small>{{ $message }}</small>@enderror
                </label>
                <label class="member-form-field">
                    <span>Quartier</span>
                    <select name="quartier_id">
                        <option value="">Choisir un quartier</option>
                        @foreach ($quartiers as $quartier)
                            <option value="{{ $quartier->id }}" @selected((string) old('quartier_id', $annonce->quartier_id) === (string) $quartier->id)>{{ $quartier->nom }} · {{ $quartier->commune?->nom }}</option>
                        @endforeach
                    </select>
                    @error('quartier_id')<small>{{ $message }}</small>@enderror
                </label>
            </div>

            <div class="member-form-grid member-form-grid-three">
                <label class="member-form-field">
                    <span>Loyer total (FCFA / mois)</span>
                    <input name="loyer_total" type="number" min="0" step="500" value="{{ old('loyer_total', $annonce->loyer_total) }}">
                    @error('loyer_total')<small>{{ $message }}</small>@enderror
                </label>
                <label class="member-form-field">
                    <span>Contribution mensuelle (FCFA)</span>
                    <input name="contribution_mensuelle" type="number" min="0" step="500" value="{{ old('contribution_mensuelle', $annonce->contribution_mensuelle) }}">
                    @error('contribution_mensuelle')<small>{{ $message }}</small>@enderror
                </label>
                <label class="member-form-field">
                    <span>Budget max. (FCFA / mois)</span>
                    <input name="budget_max" type="number" min="0" step="500" value="{{ old('budget_max', $annonce->budget_max) }}">
                    @error('budget_max')<small>{{ $message }}</small>@enderror
                </label>
            </div>

            <div class="member-form-grid">
                <label class="member-form-field">
                    <span>Type de logement</span>
                    <input name="type_logement" type="text" maxlength="255" value="{{ old('type_logement', $annonce->type_logement) }}" placeholder="Studio, appartement…">
                </label>
                <label class="member-form-field">
                    <span>Disponible à partir du</span>
                    <input name="disponible_a_partir_du" type="date" value="{{ old('disponible_a_partir_du', optional($annonce->disponible_a_partir_du)->format('Y-m-d')) }}">
                </label>
            </div>

            <div class="member-form-actions">
                <p>Votre annonce sera vérifiée avant sa publication.</p>
                <button class="admin-primary-action" type="submit">{{ $annonce->exists ? 'Enregistrer les modifications' : 'Envoyer mon annonce' }} <span aria-hidden="true">↗</span></button>
            </div>
        </form>
    </section>
</x-app-layout>