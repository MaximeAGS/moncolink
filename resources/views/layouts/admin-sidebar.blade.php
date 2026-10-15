<aside class="admin-sidebar">
    <div class="admin-brand"><x-brand-logo :href="route('admin.dashboard')" /><small>ADMINISTRATION</small></div>

    <div class="admin-sidebar-label">Espace de gestion</div>
    <nav class="admin-sidebar-nav" aria-label="Navigation administration">
        <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}"><span>⌂</span>Vue d’ensemble</a>
        <a class="admin-nav-link {{ request()->routeIs('admin.annonces.*') ? 'is-active' : '' }}" href="{{ route('admin.annonces.index') }}"><span>▤</span>Annonces</a>
        <a class="admin-nav-link {{ request()->routeIs('admin.utilisateurs.*') ? 'is-active' : '' }}" href="{{ route('admin.utilisateurs.index') }}"><span>♙</span>Utilisateurs</a>
        <a class="admin-nav-link {{ request()->routeIs('admin.conversations.*') ? 'is-active' : '' }}" href="{{ route('admin.conversations.index') }}"><span>◷</span>Conversations</a>
        <a class="admin-nav-link {{ request()->routeIs('admin.signalements.*') ? 'is-active' : '' }}" href="{{ route('admin.signalements.index') }}"><span>⚑</span>Signalements</a>
        <a class="admin-nav-link {{ request()->routeIs('admin.parametres.*') ? 'is-active' : '' }}" href="{{ route('admin.parametres.edit') }}"><span>⚙</span>Paramètres</a>

        <details class="admin-nav-group" {{ request()->routeIs('admin.messages.*', 'admin.photos-annonces.*', 'admin.favoris.*', 'admin.notifications.*', 'admin.blocages.*', 'admin.users.*') ? 'open' : '' }}>
            <summary><span>＋</span>Autres données</summary>
            <a class="admin-nav-link admin-nav-sub {{ request()->routeIs('admin.messages.*') ? 'is-active' : '' }}" href="{{ route('admin.messages.index') }}">Messages</a>
            <a class="admin-nav-link admin-nav-sub {{ request()->routeIs('admin.photos-annonces.*') ? 'is-active' : '' }}" href="{{ route('admin.photos-annonces.index') }}">Photos d’annonces</a>
            <a class="admin-nav-link admin-nav-sub {{ request()->routeIs('admin.favoris.*') ? 'is-active' : '' }}" href="{{ route('admin.favoris.index') }}">Favoris</a>
            <a class="admin-nav-link admin-nav-sub {{ request()->routeIs('admin.notifications.*') ? 'is-active' : '' }}" href="{{ route('admin.notifications.index') }}">Notifications</a>
            <a class="admin-nav-link admin-nav-sub {{ request()->routeIs('admin.blocages.*') ? 'is-active' : '' }}" href="{{ route('admin.blocages.index') }}">Blocages</a>
            <a class="admin-nav-link admin-nav-sub {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}">Comptes système</a>
        </details>
    </nav>

    <div class="admin-sidebar-bottom">
        <div class="admin-user-card">
            <span class="admin-avatar">{{ auth()->user()->initiales ?? mb_strtoupper(mb_substr(auth()->user()->email, 0, 1)) }}</span>
            <span class="admin-user-name">{{ auth()->user()->nom_complet ?? auth()->user()->email }}<small>Administrateur</small></span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="admin-logout" type="submit">Déconnexion <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</aside>