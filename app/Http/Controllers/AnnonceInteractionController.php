<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\BlocageUtilisateur;
use App\Models\Signalement;
use App\Models\Utilisateur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnnonceInteractionController extends Controller
{
    public function blockedMembers(Request $request): View
    {
        $member = $this->member($request);
        $blocages = BlocageUtilisateur::par($member->id)
            ->with('bloque')
            ->latest()
            ->paginate(20);

        return view('utilisateurs.bloques', compact('blocages'));
    }

    public function block(Request $request, Utilisateur $utilisateur): RedirectResponse
    {
        $member = $this->member($request);
        abort_if($member->is($utilisateur), 403);

        BlocageUtilisateur::firstOrCreate([
            'bloque_par' => $member->id,
            'utilisateur_bloque' => $utilisateur->id,
        ]);

        return back()->with('status', 'Les annonces de ce membre sont maintenant masquées.');
    }

    public function unblock(Request $request, Utilisateur $utilisateur): RedirectResponse
    {
        $member = $this->member($request);
        abort_if($member->is($utilisateur), 403);

        BlocageUtilisateur::query()
            ->where('bloque_par', $member->id)
            ->where('utilisateur_bloque', $utilisateur->id)
            ->firstOrFail()
            ->delete();

        return back()->with('status', 'Ce membre a été débloqué.');
    }

    public function report(Request $request, Annonce $annonce): RedirectResponse
    {
        $member = $this->member($request);
        abort_unless($annonce->statut === 'publiee', 404);
        abort_if((int) $annonce->utilisateur_id === (int) $member->id, 403);

        $validated = $request->validate([
            'motif' => ['required', Rule::in([
                'suspecte',
                'trompeuse',
                'photos_inappropriees',
                'comportement_abusif',
                'spam',
                'autre',
            ])],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $alreadyReported = Signalement::query()
            ->where('signale_par', $member->id)
            ->where('signalable_type', Annonce::class)
            ->where('signalable_id', $annonce->id)
            ->whereIn('statut', ['en_attente', 'en_examen'])
            ->exists();

        if (! $alreadyReported) {
            Signalement::create([
                'signale_par' => $member->id,
                'signalable_type' => Annonce::class,
                'signalable_id' => $annonce->id,
                'motif' => $validated['motif'],
                'details' => $validated['details'] ?? null,
                'statut' => 'en_attente',
            ]);
        }

        return back()->with('status', 'Merci. Votre signalement a été transmis à la modération.');
    }

    private function member(Request $request): Utilisateur
    {
        abort_unless($request->user() instanceof Utilisateur, 403);

        return $request->user();
    }
}
