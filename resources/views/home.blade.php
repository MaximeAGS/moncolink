@extends('layouts.home')

@section('content')
    <section class="market-hero" aria-labelledby="hero-title">
        <img class="market-hero-image" src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=2200&q=88" alt="Appartement lumineux prêt à accueillir une colocation" fetchpriority="high">
        <div class="market-hero-shade"></div>
        <div class="market-hero-copy">
            <p class="market-kicker"><span></span>{{ $branding?->slogan ?: 'La colocation, plus proche de vous.' }}</p>
            <h1 id="hero-title">Trouvez votre<br>place au <em>Bénin.</em></h1>
            <p>{{ $branding?->description ?: 'Explorez les annonces de colocation et trouvez un logement qui correspond à votre budget et votre quotidien.' }}</p>
            <div class="market-hero-actions">
                <a class="market-button market-button-light" href="#annonces">Découvrir les annonces <span aria-hidden="true">↓</span></a>
                @guest
                    <a class="market-text-link" href="{{ route('register') }}">Créer un compte <span aria-hidden="true">↗</span></a>
                @endguest
            </div>
        </div>
        <div class="market-photo-note"><span>01</span> Un nouveau départ commence chez soi.</div>
    </section>

    @if (session('status'))
        <div class="market-feedback" role="status">{{ session('status') }}</div>
    @endif

    <section class="market-search-section" aria-label="Rechercher une colocation">
        <div class="market-search-heading">
            <p class="market-kicker"><span></span> Votre recherche</p>
            <h2>Un logement en tête ?</h2>
        </div>
        <form class="market-search-form" action="{{ url('/') }}" method="GET">
            <label class="market-search-field" for="ville_id">
                <span>Ville</span>
                <select id="ville_id" name="ville_id">
                    <option value="">Toutes les villes</option>
                    @foreach ($villes as $ville)
                        <option value="{{ $ville->id }}" @selected((string) request('ville_id') === (string) $ville->id)>{{ $ville->nom }}</option>
                    @endforeach
                </select>
            </label>
            <label class="market-search-field" for="type">
                <span>Type d’annonce</span>
                <select id="type" name="type">
                    <option value="">Toutes les annonces</option>
                    <option value="offre" @selected(request('type') === 'offre')>Chambre disponible</option>
                    <option value="recherche" @selected(request('type') === 'recherche')>Recherche de colocation</option>
                </select>
            </label>
            <label class="market-search-field market-budget-field" for="budget_max">
                <span>Budget mensuel max.</span>
                <input id="budget_max" name="budget_max" type="number" min="0" step="5000" placeholder="Ex. 50 000" value="{{ request('budget_max') }}">
            </label>
            <button class="market-search-button" type="submit"><span aria-hidden="true">⌕</span> Rechercher</button>
        </form>
    </section>

    <section class="market-stats" aria-label="La plateforme en chiffres">
        <div class="market-stat"><strong>{{ number_format($annonceCount) }}</strong><span>Annonces publiées</span></div>
        <div class="market-stat"><strong>{{ number_format($memberCount) }}</strong><span>Membres inscrits</span></div>
        <div class="market-stat"><strong>{{ number_format($cityCount) }}</strong><span>Villes couvertes</span></div>
        <p>Des repères concrets pour trouver votre prochaine colocation.</p>
    </section>

    <section class="market-listings" id="annonces" aria-labelledby="listings-title">
        <header class="market-section-heading">
            <div>
                <p class="market-kicker"><span></span>{{ $searchWasSubmitted ? 'Résultats de recherche' : 'À découvrir' }}</p>
                <h2 id="listings-title">{{ $searchWasSubmitted ? 'Les annonces qui correspondent' : 'Les dernières annonces' }}</h2>
            </div>
            <span class="market-result-count">{{ $annonces->total() }} annonce{{ $annonces->total() === 1 ? '' : 's' }}</span>
        </header>

        @if ($annonces->isNotEmpty())
            <div class="market-listing-grid">
                @foreach ($annonces as $annonce)
                    @php($prix = $annonce->contribution_mensuelle ?? $annonce->budget_max ?? $annonce->loyer_total)
                    <article class="market-listing-card">
                        <div class="market-listing-photo">
                            @if ($annonce->photoPrincipale?->chemin)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($annonce->photoPrincipale->chemin) }}" alt="Photo de {{ $annonce->titre }}" loading="lazy">
                            @else
                                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=760&q=75" alt="Intérieur de colocation" loading="lazy">
                            @endif
                            <span class="market-listing-type">{{ $annonce->type === 'offre' ? 'Chambre disponible' : 'Recherche' }}</span>
                        </div>
                        <div class="market-listing-body">
                            <p class="market-listing-location">{{ collect([$annonce->quartier?->nom, $annonce->commune?->nom, $annonce->ville?->nom])->filter()->join(' · ') ?: 'Bénin' }}</p>
                            <h3>{{ $annonce->titre }}</h3>
                            <p class="market-listing-author">Par {{ $annonce->utilisateur?->nom_complet ?? 'Membre' }}</p>
                            <p class="market-listing-description">{{ \Illuminate\Support\Str::limit($annonce->description, 105) }}</p>
                            <div class="market-listing-bottom">
                                <strong>@if ($prix) {{ number_format($prix, 0, ',', ' ') }} <small>FCFA / mois</small> @else Budget à préciser @endif</strong>
                                <span>{{ $annonce->type === 'offre' ? 'Offre' : 'Recherche' }}</span>
                            </div>
                            @auth
                                @if (method_exists(auth()->user(), 'conversationsEnvoyees') && (int) auth()->id() !== (int) $annonce->utilisateur_id)
                                    <form class="market-contact-form" method="POST" action="{{ route('annonces.conversations.store', $annonce) }}">
                                        @csrf
                                        <button class="market-contact-button" type="submit">Contacter l’auteur <span aria-hidden="true">↗</span></button>
                                    </form>
                                @elseif ((int) auth()->id() === (int) $annonce->utilisateur_id)
                                    <span class="market-own-listing">Votre annonce</span>
                                @endif
                            @else
                                <a class="market-contact-button" href="{{ route('login') }}">Connectez-vous pour contacter <span aria-hidden="true">↗</span></a>
                            @endauth
                            @if ($canInteractWithAnnonces && (int) auth()->id() !== (int) $annonce->utilisateur_id)
                                <div class="market-safety-actions">
                                    <form method="POST" action="{{ route('utilisateurs.blocages.store', $annonce->utilisateur) }}" onsubmit="return confirm('Masquer toutes les annonces de ce membre ?')">
                                        @csrf
                                        <button class="market-safety-button" type="submit">Bloquer ce membre</button>
                                    </form>

                                    @if (in_array($annonce->id, $reportedAnnonceIds))
                                        <span class="market-reported-label">Signalement envoyé</span>
                                    @else
                                        <details class="market-report-control">
                                            <summary>Signaler cette annonce</summary>
                                            <form class="market-report-form" method="POST" action="{{ route('annonces.signalements.store', $annonce) }}">
                                                @csrf
                                                <label class="sr-only" for="motif-{{ $annonce->id }}">Motif du signalement</label>
                                                <select id="motif-{{ $annonce->id }}" name="motif" required>
                                                    <option value="">Choisir un motif</option>
                                                    <option value="suspecte">Annonce suspecte</option>
                                                    <option value="trompeuse">Informations trompeuses</option>
                                                    <option value="photos_inappropriees">Photos inappropriées</option>
                                                    <option value="comportement_abusif">Comportement abusif</option>
                                                    <option value="spam">Spam</option>
                                                    <option value="autre">Autre</option>
                                                </select>
                                                <label class="sr-only" for="details-{{ $annonce->id }}">Précisions facultatives</label>
                                                <textarea id="details-{{ $annonce->id }}" name="details" rows="2" maxlength="2000" placeholder="Précisions (facultatif)"></textarea>
                                                <button type="submit">Envoyer à la modération</button>
                                            </form>
                                        </details>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="market-pagination">{{ $annonces->links() }}</div>
        @else
            <div class="market-empty-state">
                <span class="market-empty-mark" aria-hidden="true">⌕</span>
                <h3>{{ $searchWasSubmitted ? 'Aucune annonce ne correspond à ces critères.' : 'Les nouvelles annonces arrivent bientôt.' }}</h3>
                <p>{{ $searchWasSubmitted ? 'Essayez une autre ville ou ajustez votre budget.' : 'Revenez consulter les offres et recherches publiées par la communauté.' }}</p>
                @if ($searchWasSubmitted)
                    <a href="{{ url('/') }}#annonces">Effacer les filtres <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        @endif
    </section>

    <section class="market-join">
        <p class="market-kicker"><span></span>{{ $branding?->slogan ?: 'La colocation, plus proche de vous.' }}</p>
        <h2>Votre prochain chez-vous<br>commence par une rencontre.</h2>
        <p>{{ $branding?->description ?: 'Rejoignez la communauté et publiez votre recherche de colocation.' }}</p>
        <a class="market-button market-button-light" href="{{ route('register') }}">Créer mon compte <span aria-hidden="true">↗</span></a>
    </section>
@endsection