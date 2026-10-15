<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParametreApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ParametreApplicationController extends Controller
{
    public function edit(): View
    {
        $parametres = ParametreApplication::firstOrCreate(
            ['id' => 1],
            ['nom' => config('app.name')]
        );

        return view('admin.parametres.edit', compact('parametres'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'slogan' => ['nullable', 'string', 'max:160'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'supprimer_logo' => ['nullable', 'boolean'],
        ]);

        $parametres = ParametreApplication::firstOrCreate(
            ['id' => 1],
            ['nom' => config('app.name')]
        );

        if ($request->boolean('supprimer_logo') && $parametres->logo) {
            Storage::disk('public')->delete($parametres->logo);
            $parametres->logo = null;
        }

        if ($request->hasFile('logo')) {
            if ($parametres->logo) {
                Storage::disk('public')->delete($parametres->logo);
            }

            $parametres->logo = $request->file('logo')->storePublicly('branding', 'public');
        }

        $parametres->nom = $validated['nom'];
        $parametres->description = $validated['description'] ?? null;
        $parametres->slogan = $validated['slogan'] ?? null;
        $parametres->save();

        Cache::forget('application_branding');

        return redirect()->route('admin.parametres.edit')
            ->with('status', 'Les paramètres du projet ont été enregistrés.');
    }
}
