<div>
    <!-- Mobile toggle button -->
    <button id="btnOpenSidebar" aria-controls="sidebar-multi-level-sidebar" type="button" class="text-heading bg-transparent border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-md ms-3 mt-3 text-sm p-2 focus:outline-none inline-flex sm:hidden" onclick="toggleSidebar()">
       <span class="sr-only">Open sidebar</span>
       <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h10"/>
       </svg>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar-multi-level-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transform -translate-x-full sm:translate-x-0 transition-transform bg-neutral-primary-soft border-r border-default" aria-label="Sidebar" aria-hidden="true">
       <div class="h-full px-3 py-4 overflow-y-auto">
          <ul class="space-y-2 font-medium">
             <li>
                <a href="" class="flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group">
                   <span class="inline-flex items-center justify-center w-6 h-6">
                      <i class="fa-light fa-rocket-launch"></i>
                   </span>
                   <span class="ms-3">Ganhe R$ 50 grátis</span>
                </a>
             </li>

             <li>
                <a href="{{ url('/') }}" title="Visão Geral" class="flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group {{ Request::is('/') ? 'active-sidebar' : '' }}">
                   <img src="{{ asset('/assets/images/svg/home2.svg') }}" alt="" class="w-6 h-6">
                   <span class="ms-3">Visão geral</span>
                </a>
             </li>

             <li>
                <a href="{{ url('painel/affiliates') }}" title="Menu de Afiliado" class="flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('panel.affiliates.index') ? 'active-sidebar' : '' }}">
                   <img src="{{ asset('/assets/images/svg/affiliate.svg') }}" alt="" class="w-6 h-6">
                   <span class="ms-3">Menu de Afiliado</span>
                </a>
             </li>

             <li>
                <a href="{{ url('/como-funciona') }}" title="Como funciona?" class="flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group {{ Request::is('/como-funciona') ? 'active-sidebar' : '' }}">
                   <img src="{{ asset('assets/images/svg/about.svg') }}" alt="" class="w-6 h-6">
                   <span class="ms-3">Como funciona?</span>
                </a>
             </li>

             <li>
                <a href="{{ url('/suporte') }}" title="Suporte" class="flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group {{ Request::is('/suporte') ? 'active-sidebar' : '' }}">
                   <img src="{{ asset('assets/images/svg/suporte.svg') }}" alt="" class="w-6 h-6">
                   <span class="ms-3">Suporte</span>
                </a>
             </li>

             <li>
                <a href="{{ url('/sobre-nos') }}" title="Sobre Nós" class="flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group {{ Request::is('/sobre-nos') ? 'active-sidebar' : '' }}">
                   <img src="{{ asset('assets/images/svg/sobre.svg') }}" alt="" class="w-6 h-6">
                   <span class="ms-3">Sobre Nós</span>
                </a>
             </li>

             @if(\App\Models\GameExclusive::count() > 0)
                <li>
                   <button type="button" class="flex items-center w-full justify-between px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group" aria-controls="casino-dropdown" data-collapse-toggle="casino-dropdown">
                         <span class="flex items-center">
                             <img src="{{ asset('assets/images/svg/game.svg') }}" class="w-5 h-5 shrink-0" alt="">
                             <span class="ms-3">CASSINO</span>
                         </span>
                         <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                   </button>
                   <ul id="casino-dropdown" class="hidden py-2 space-y-2">
                       @foreach(\App\Models\GameExclusive::limit(10)->orderBy('views','desc')->where('active',1)->get() as $gameExclusive)
                           <li>
                               <a href="{{ route('web.vgames.show', ['game' => $gameExclusive->uuid]) }}" class="pl-10 flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group">
                                   <img src="{{ asset('storage/'.$gameExclusive->icon) }}" alt="" class="w-6 h-6 mr-2"> {{ $gameExclusive->name }}
                               </a>
                           </li>
                       @endforeach
                   </ul>
                </li>
             @endif

             @if(\App\Models\Category::count() > 0)
                <li>
                   <button type="button" class="flex items-center w-full justify-between px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group" aria-controls="cats-dropdown" data-collapse-toggle="cats-dropdown">
                         <span class="flex items-center">
                             <img src="{{ asset('assets/images/svg/categories.svg') }}" class="w-5 h-5 shrink-0" alt="">
                             <span class="ms-3">CATEGORIAS</span>
                         </span>
                         <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                   </button>
                   <ul id="cats-dropdown" class="hidden py-2 space-y-2">
                       @foreach(\App\Models\Category::all() as $category)
                           <li>
                               <a href="{{ route('web.category.index', ['slug' => $category->slug]) }}" class="pl-10 flex items-center px-2 py-1.5 text-body rounded-md hover:bg-neutral-tertiary hover:text-fg-brand group">{{ $category->name }}</a>
                           </li>
                       @endforeach
                   </ul>
                </li>
             @endif

          </ul>
       </div>
    </aside>

    <!-- Backdrop for mobile -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden" onclick="toggleSidebar()" aria-hidden="true"></div>

    <script>
      function toggleSidebar(){
         var aside = document.getElementById('sidebar-multi-level-sidebar');
         var backdrop = document.getElementById('sidebar-backdrop');
         if(!aside) return;
         var isHidden = aside.classList.contains('-translate-x-full');
         if(isHidden){
            // move to body to avoid fixed positioning being clipped by transformed ancestors
            try{
               if(aside.parentElement && aside.parentElement !== document.body){
                  window._sidebarOriginal = {
                     parent: aside.parentElement,
                     nextSibling: aside.nextElementSibling
                  };
                  document.body.appendChild(aside);
               }
            }catch(e){}

            aside.classList.remove('-translate-x-full');
            aside.classList.add('full-width');
            aside.setAttribute('aria-hidden','false');
            // force full-screen inline styles as backup
            aside.style.position = 'fixed';
            aside.style.top = '0';
            aside.style.left = '0';
            aside.style.right = '0';
            aside.style.bottom = '0';
            aside.style.width = '100%';
            aside.style.height = '100vh';
            aside.style.zIndex = '2147483600';
            if(backdrop) backdrop.classList.remove('hidden');
            document.body.classList.add('sidebar-open');
         } else {
            aside.classList.add('-translate-x-full');
            aside.classList.remove('full-width');
            aside.setAttribute('aria-hidden','true');
            if(backdrop) backdrop.classList.add('hidden');
            document.body.classList.remove('sidebar-open');
            // remove inline styles
            try{
               aside.style.position = '';
               aside.style.top = '';
               aside.style.left = '';
               aside.style.right = '';
               aside.style.bottom = '';
               aside.style.width = '';
               aside.style.height = '';
               aside.style.zIndex = '';
            }catch(e){}
            // restore to original parent if moved
            try{
               if(window._sidebarOriginal && window._sidebarOriginal.parent){
                  var orig = window._sidebarOriginal;
                  if(orig.nextSibling) orig.parent.insertBefore(aside, orig.nextSibling);
                  else orig.parent.appendChild(aside);
                  delete window._sidebarOriginal;
               }
            }catch(e){}
         }
      }

        // Collapse toggles
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('[data-collapse-toggle]').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    var targetId = btn.getAttribute('data-collapse-toggle') || btn.getAttribute('aria-controls');
                    if(!targetId) return;
                    var el = document.getElementById(targetId);
                    if(!el) return;
                    el.classList.toggle('hidden');
                });
            });
        });
    </script>
</div>
