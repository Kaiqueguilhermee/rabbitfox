<nav class="mobile-bottom-nav d-md-none">
    <a href="{{ url('/') }}" class="mobile-nav-item {{ request()->is('/') ? 'active' : '' }}" aria-label="Início">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V9.5z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Início</span>
    </a>

    <a href="{{ url('/games') }}" class="mobile-nav-item {{ request()->is('games*') ? 'active' : '' }}" aria-label="Jogos">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 8v8a2 2 0 0 1-2 2h-4l-2 2-2-2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="8.5" cy="12.5" r="1" fill="currentColor"/><circle cx="15.5" cy="10.5" r="1" fill="currentColor"/></svg>
        <span>Jogos</span>
    </a>

    <a href="{{ route('wallet.index') }}" class="mobile-nav-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}" aria-label="Carteira">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="6" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 11h.01" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Carteira</span>
        @if(Auth::check() && loatval(\\Helper::getBalance()) > 0)
            <span class="nav-badge">+</span>
        @endif
    </a>

    <a href="{{ route('panel.profile.index') }}" class="mobile-nav-item {{ request()->routeIs('panel.profile.*') ? 'active' : '' }}" aria-label="Perfil">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="7" r="3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Perfil</span>
    </a>
</nav>

<style>
.mobile-bottom-nav{position:fixed;bottom:0;left:0;right:0;height:64px;background:linear-gradient(180deg,#0f1724,#071025);display:flex;gap:6px;padding:8px 10px;border-top:1px solid rgba(255,255,255,0.04);z-index:1100}
.mobile-bottom-nav .mobile-nav-item{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#cbd5e1;text-decoration:none;border-radius:10px;padding:6px 6px;font-size:.75rem}
.mobile-bottom-nav .mobile-nav-item svg{margin-bottom:4px;color:inherit}
.mobile-bottom-nav .mobile-nav-item.active{background:linear-gradient(90deg, rgba(35,171,14,0.12), rgba(35,171,14,0.06));color:#e6ffed;box-shadow:0 6px 18px rgba(16,185,129,0.06)}
.mobile-bottom-nav .mobile-nav-item .nav-badge{position:absolute;top:6px;right:14px;background:#16a34a;color:#fff;border-radius:8px;padding:2px 6px;font-size:0.7rem}
@media (min-width:768px){ .mobile-bottom-nav{display:none} }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // make clicks provide immediate active feedback (SPA like)
    document.querySelectorAll('.mobile-bottom-nav .mobile-nav-item').forEach(function(el){
        el.addEventListener('click', function(){
            document.querySelectorAll('.mobile-bottom-nav .mobile-nav-item').forEach(function(i){ i.classList.remove('active'); });
            el.classList.add('active');
        });
    });
});
</script>