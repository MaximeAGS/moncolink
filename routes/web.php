<?php

use App\Http\Controllers\Admin\AnnonceController;
use App\Http\Controllers\Admin\BlocageUtilisateurController;
use App\Http\Controllers\Admin\ConversationController as AdminConversationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FavoriController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ParametreApplicationController;
use App\Http\Controllers\Admin\PhotoAnnonceController;
use App\Http\Controllers\Admin\SignalementController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UtilisateurController;
use App\Http\Controllers\AnnonceInteractionController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MesAnnoncesController;
use App\Http\Controllers\ProfileController;
use App\Models\Annonce;
use App\Models\BlocageUtilisateur;
use App\Models\Conversation;
use App\Models\Favori;
use App\Models\Message;
use App\Models\Signalement;
use App\Models\Utilisateur;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $filters = $request->validate([
        'ville_id' => ['nullable', 'integer', 'exists:villes,id'],
        'type' => ['nullable', 'in:offre,recherche'],
        'budget_max' => ['nullable', 'numeric', 'min:0'],
    ]);

    $member = $request->user();
    $canInteractWithAnnonces = $member instanceof Utilisateur;
    $blockedUserIds = $canInteractWithAnnonces
        ? BlocageUtilisateur::par($member->id)->pluck('utilisateur_bloque')->all()
        : [];
    $reportedAnnonceIds = $canInteractWithAnnonces
        ? Signalement::query()
            ->where('signale_par', $member->id)
            ->where('signalable_type', Annonce::class)
            ->whereIn('statut', ['en_attente', 'en_examen'])
            ->pluck('signalable_id')
            ->all()
        : [];

    $annoncesQuery = Annonce::query()
        ->where('statut', 'publiee')
        ->with(['photoPrincipale', 'ville', 'commune', 'quartier', 'utilisateur']);

    if ($blockedUserIds) {
        $annoncesQuery->whereNotIn('utilisateur_id', $blockedUserIds);
    }

    if (! empty($filters['ville_id'])) {
        $annoncesQuery->where('ville_id', $filters['ville_id']);
    }

    if (! empty($filters['type'])) {
        $annoncesQuery->where('type', $filters['type']);
    }

    if (isset($filters['budget_max'])) {
        $budget = $filters['budget_max'];
        $annoncesQuery->where(function ($query) use ($budget) {
            $query->where(function ($offers) use ($budget) {
                $offers->where('type', 'offre')
                    ->where(function ($amount) use ($budget) {
                        $amount->where('contribution_mensuelle', '<=', $budget)
                            ->orWhere(function ($fallback) use ($budget) {
                                $fallback->whereNull('contribution_mensuelle')
                                    ->where('loyer_total', '<=', $budget);
                            });
                    });
            })->orWhere(function ($searches) use ($budget) {
                $searches->where('type', 'recherche')
                    ->where(function ($amount) use ($budget) {
                        $amount->where('budget_max', '<=', $budget)
                            ->orWhere(function ($fallback) use ($budget) {
                                $fallback->whereNull('budget_max')
                                    ->where(function ($monthly) use ($budget) {
                                        $monthly->where('contribution_mensuelle', '<=', $budget)
                                            ->orWhere(function ($rent) use ($budget) {
                                                $rent->whereNull('contribution_mensuelle')
                                                    ->where('loyer_total', '<=', $budget);
                                            });
                                    });
                            });
                    });
            });
        });
    }

    return view('home', [
        'annonces' => $annoncesQuery->latest()->paginate(6)->withQueryString(),
        'villes' => Ville::query()->orderBy('nom')->get(['id', 'nom']),
        'annonceCount' => Annonce::query()->where('statut', 'publiee')->count(),
        'memberCount' => Utilisateur::query()->where('statut', 'actif')->count(),
        'cityCount' => Ville::query()
            ->whereHas('annonces', fn ($query) => $query->where('statut', 'publiee'))
            ->count(),
        'searchWasSubmitted' => $request->hasAny(['ville_id', 'type', 'budget_max']),
        'blockedUserIds' => $blockedUserIds,
        'reportedAnnonceIds' => $reportedAnnonceIds,
        'canInteractWithAnnonces' => $canInteractWithAnnonces,
    ]);
});

Route::get('/dashboard', function () {
    $user = request()->user();
    if (method_exists($user, 'hasRole') && $user->hasRole('administrateur')) {
        return redirect()->route('admin.dashboard');
    }

    $hasColocationProfile = method_exists($user, 'annonces');
    $annonces = $hasColocationProfile
        ? $user->annonces()->with(['ville', 'commune', 'quartier'])->latest()->take(4)->get()
        : collect();
    $conversations = $hasColocationProfile
        ? Conversation::query()
            ->where(fn ($query) => $query->where('initiateur_id', $user->getKey())
                ->orWhere('destinataire_id', $user->getKey()))
            ->with(['annonce', 'initiateur', 'destinataire', 'dernierMessage'])
            ->orderByDesc('dernier_message_le')
            ->take(4)
            ->get()
        : collect();
    $annonceCount = $hasColocationProfile ? $user->annonces()->count() : 0;
    $publishedCount = $hasColocationProfile ? $user->annonces()->where('statut', 'publiee')->count() : 0;
    $conversationCount = $hasColocationProfile
        ? $user->conversationsEnvoyees()->count() + $user->conversationsRecues()->count()
        : 0;
    $favoriteCount = $hasColocationProfile
        ? Favori::query()->where('utilisateur_id', $user->getKey())->count()
        : 0;
    $unreadMessageCount = $hasColocationProfile
        ? Message::query()
            ->whereNull('lu_le')
            ->where('expediteur_id', '!=', $user->getKey())
            ->whereHas('conversation', fn ($query) => $query
                ->where('initiateur_id', $user->getKey())
                ->orWhere('destinataire_id', $user->getKey()))
            ->count()
        : 0;

    return view('dashboard', compact(
        'user',
        'annonces',
        'conversations',
        'annonceCount',
        'publishedCount',
        'conversationCount',
        'favoriteCount',
        'unreadMessageCount',
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/membres-bloques', [AnnonceInteractionController::class, 'blockedMembers'])
        ->name('membres-bloques.index');
    Route::post('/utilisateurs/{utilisateur}/blocage', [AnnonceInteractionController::class, 'block'])
        ->name('utilisateurs.blocages.store');
    Route::delete('/utilisateurs/{utilisateur}/blocage', [AnnonceInteractionController::class, 'unblock'])
        ->name('utilisateurs.blocages.destroy');
    Route::post('/annonces/{annonce}/signalements', [AnnonceInteractionController::class, 'report'])
        ->name('annonces.signalements.store');
    Route::get('/mes-annonces/creer', [MesAnnoncesController::class, 'create'])->name('mes-annonces.create');
    Route::post('/mes-annonces', [MesAnnoncesController::class, 'store'])->name('mes-annonces.store');
    Route::get('/mes-annonces/{annonce}/modifier', [MesAnnoncesController::class, 'edit'])->name('mes-annonces.edit');
    Route::put('/mes-annonces/{annonce}', [MesAnnoncesController::class, 'update'])->name('mes-annonces.update');
    Route::delete('/mes-annonces/{annonce}', [MesAnnoncesController::class, 'destroy'])->name('mes-annonces.destroy');
    Route::post('/annonces/{annonce}/conversation', [ConversationController::class, 'startFromAnnonce'])
        ->name('annonces.conversations.store');
    Route::get('/conversations', [ConversationController::class, 'index'])
        ->name('conversations.index');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])
        ->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'storeMessage'])
        ->name('conversations.messages.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->middleware('role:administrateur')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/parametres', [ParametreApplicationController::class, 'edit'])->name('parametres.edit');
        Route::put('/parametres', [ParametreApplicationController::class, 'update'])->name('parametres.update');
        Route::resource('annonces', AnnonceController::class);
        Route::resource('utilisateurs', UtilisateurController::class);
        Route::resource('users', UserController::class);
        Route::resource('photos-annonces', PhotoAnnonceController::class);
        Route::resource('conversations', AdminConversationController::class);
        Route::resource('messages', MessageController::class);
        Route::resource('signalements', SignalementController::class);
        Route::resource('favoris', FavoriController::class);
        Route::resource('notifications', NotificationController::class);
        Route::resource('blocages', BlocageUtilisateurController::class);
    });
});

require __DIR__.'/auth.php';
