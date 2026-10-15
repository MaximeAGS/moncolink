<aside class="admin-sidebar member-sidebar">
    <div class="admin-brand">
        <x-brand-logo :href="route('dashboard')" />
        <small>ESPACE MEMBRE</small>
    </div>

    <div class="admin-sidebar-label">Mon espace</div>
    <nav class="admin-sidebar-nav" aria-label="Navigation membre">
        <a class="admin-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><span>⌂</span>Vue d’ensemble</a>
        <a class="admin-nav-link {{ request()->routeIs('mes-annonces.*') ? 'is-active' : '' }}" href="{{ route('dashboard') }}#mes-annonces"><span>▤</span>Mes annonces</a>
        <a class="admin-nav-link" href="{{ route('mes-annonces.create') }}"><span>＋</span>Publier une annonce</a>
        <a class="admin-nav-link {{ request()->routeIs('conversations.*') ? 'is-active' : '' }}" href="{{ route('conversations.index') }}"><span>◷</span>Discussions</a>
        <a class="admin-nav-link {{ request()->routeIs('membres-bloques.*') ? 'is-active' : '' }}" href="{{ route('membres-bloques.index') }}"><span>⊘</span>Membres bloqués</a>
        <a class="admin-nav-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" href="{{ route('profile.edit') }}"><span>♙</span>Mon profil</a>
    </nav>

    <div class="admin-sidebar-bottom">
        <a class="member-site-link" href="{{ url('/') }}">Voir le site <span aria-hidden="true">↗</span></a>
        <div class="admin-user-card">
            <span class="admin-avatar">{{ auth()->user()->initiales ?? mb_strtoupper(mb_substr(auth()->user()->email, 0, 1)) }}</span>
            <span class="admin-user-name">{{ auth()->user()->nom_complet ?? auth()->user()->email }}<small>Membre</small></span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="admin-logout" type="submit">Déconnexion <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</aside>