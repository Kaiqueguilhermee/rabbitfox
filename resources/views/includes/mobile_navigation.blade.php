<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav d-md-none">
    <a href="{{ url('/') }}" class="mobile-nav-item {{ request()->is('/') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Início</span>
    </a>
    <a href="{{ route('web.game.list') }}" class="mobile-nav-item {{ request()->routeIs('web.game.list') ? 'active' : '' }}">
        <i class="fas fa-gamepad"></i>
        <span>Jogos</span>
    </a>
    <a href="{{ route('panel.wallet.index') }}" class="mobile-nav-item {{ request()->routeIs('panel.wallet.index') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i>
        <span>Carteira</span>
    </a>
    <a href="{{ route('panel.profile.index') }}" class="mobile-nav-item {{ request()->routeIs('panel.profile.index') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span>Perfil</span>
    </a>
</nav>

<style>
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #1a1a1a;
    border-top: 1px solid #333;
    display: flex;
    justify-content: space-around;
    padding: 8px 0;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
}

.mobile-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #888;
    text-decoration: none;
    padding: 5px 10px;
    transition: all 0.3s ease;
    flex: 1;
}

.mobile-nav-item i {
    font-size: 20px;
    margin-bottom: 4px;
}

.mobile-nav-item span {
    font-size: 11px;
    font-weight: 500;
}

.mobile-nav-item:hover,
.mobile-nav-item.active {
    color: #22c55e;
}

/* Add padding to bottom of page content on mobile */
@media (max-width: 767.98px) {
    .page__content {
        padding-bottom: 70px !important;
    }
}
</style>
