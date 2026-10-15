<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Conversation;
use App\Models\Signalement;
use App\Models\Utilisateur;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'annonceCount' => Annonce::count(),
            'pendingAnnonceCount' => Annonce::where('statut', 'en_attente')->count(),
            'userCount' => Utilisateur::count(),
            'activeUserCount' => Utilisateur::where('statut', 'actif')->count(),
            'conversationCount' => Conversation::count(),
            'reportCount' => Signalement::whereIn('statut', ['en_attente', 'en_examen'])->count(),
            'latestAnnonces' => Annonce::with(['utilisateur', 'ville'])->latest()->take(5)->get(),
            'latestSignalements' => Signalement::with('auteur')->latest()->take(4)->get(),
        ]);
    }
}
