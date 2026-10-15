<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Commune;
use App\Models\Quartier;
use App\Models\Ville;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MesAnnoncesController extends Controller
{
    public function create(): View
    {
        return $this->formView(new Annonce);
    }

    public function store(Request $request): RedirectResponse
    {
        $annonce = $request->user()->annonces()->create(
            $this->validatedData($request) + ['statut' => 'en_attente']
        );

        return redirect()->route('dashboard')
            ->with('status', 'Votre annonce a été envoyée pour validation.');
    }

    public function edit(Request $request, Annonce $annonce): View
    {
        $this->authorizeOwner($request, $annonce);

        return $this->formView($annonce);
    }

    public function update(Request $request, Annonce $annonce): RedirectResponse
    {
        $this->authorizeOwner($request, $annonce);
        $annonce->fill($this->validatedData($request));
        $annonce->statut = 'en_attente';
        $annonce->publiee_le = null;
        $annonce->save();

        return redirect()->route('dashboard')
            ->with('status', 'Votre annonce a été modifiée et renvoyée pour validation.');
    }

    public function destroy(Request $request, Annonce $annonce): RedirectResponse
    {
        $this->authorizeOwner($request, $annonce);
        $annonce->delete();

        return redirect()->route('dashboard')
            ->with('status', 'Votre annonce a été supprimée.');
    }

    private function formView(Annonce $annonce): View
    {
        return view('annonces.form', [
            'annonce' => $annonce,
            'villes' => Ville::query()->orderBy('nom')->get(),
            'communes' => Commune::query()->with('ville')->orderBy('nom')->get(),
            'quartiers' => Quartier::query()->with('commune')->orderBy('nom')->get(),
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['offre', 'recherche'])],
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'type_logement' => ['nullable', 'string', 'max:255'],
            'ville_id' => ['nullable', 'integer', 'exists:villes,id'],
            'commune_id' => [
                'nullable',
                'integer',
                Rule::exists('communes', 'id')->where('ville_id', $request->input('ville_id')),
            ],
            'quartier_id' => [
                'nullable',
                'integer',
                Rule::exists('quartiers', 'id')->where('commune_id', $request->input('commune_id')),
            ],
            'loyer_total' => ['nullable', 'numeric', 'min:0'],
            'contribution_mensuelle' => ['nullable', 'numeric', 'min:0'],
            'charges_supplementaires' => ['nullable', 'numeric', 'min:0'],
            'disponible_a_partir_du' => ['nullable', 'date'],
            'capacite' => ['nullable', 'integer', 'between:1,255'],
            'equipements' => ['nullable', 'array'],
            'equipements.*' => ['string', 'max:100'],
            'conditions_particulieres' => ['nullable', 'string', 'max:5000'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
            'date_emmenagement' => ['nullable', 'date'],
            'type_logement_recherche' => ['nullable', 'string', 'max:255'],
            'preferences_vie' => ['nullable', 'array'],
            'preferences_vie.*' => ['string', 'max:100'],
            'informations_complementaires' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function authorizeOwner(Request $request, Annonce $annonce): void
    {
        abort_unless((int) $annonce->utilisateur_id === (int) $request->user()->getKey(), 404);
    }
}
