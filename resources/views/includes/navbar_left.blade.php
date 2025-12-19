<!-- Bootstrap Offcanvas Sidebar -->
<div>
    <!-- Toggle button (mobile) -->
    <button class="text-heading bg-transparent border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-md ms-3 mt-3 text-sm p-2 focus:outline-none inline-flex sm:hidden" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h10"/></svg>
    </button>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel">Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="h-full px-3 py-4 overflow-y-auto">
                <ul class="list-unstyled ps-2 mb-0">
                    <li class="mb-3">
                        <a href="" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none">
                            <i class="fa-light fa-rocket-launch"></i>
                            <span>Ganhe R$ 50 grátis</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none {{ Request::is('/') ? 'active-sidebar' : '' }}">
                            <img src="{{ asset('/assets/images/svg/home2.svg') }}" class="me-2" width="20" alt="">
                            <span>Visão geral</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('painel/affiliates') }}" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none {{ request()->routeIs('panel.affiliates.index') ? 'active-sidebar' : '' }}">
                            <img src="{{ asset('/assets/images/svg/affiliate.svg') }}" class="me-2" width="20" alt="">
                            <span>Menu de Afiliado</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/como-funciona') }}" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none {{ Request::is('/como-funciona') ? 'active-sidebar' : '' }}">
                            <img src="{{ asset('assets/images/svg/about.svg') }}" class="me-2" width="20" alt="">
                            <span>Como funciona?</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/suporte') }}" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none {{ Request::is('/suporte') ? 'active-sidebar' : '' }}">
                            <img src="{{ asset('assets/images/svg/suporte.svg') }}" class="me-2" width="20" alt="">
                            <span>Suporte</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/sobre-nos') }}" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none {{ Request::is('/sobre-nos') ? 'active-sidebar' : '' }}">
                            <img src="{{ asset('assets/images/svg/sobre.svg') }}" class="me-2" width="20" alt="">
                            <span>Sobre Nós</span>
                        </a>
                    </li>

                    @if(\App\Models\GameExclusive::count() > 0)
                        <li class="mt-3">
                            <button class="btn btn-toggle w-100 text-start d-flex justify-content-between align-items-center px-2" data-bs-toggle="collapse" data-bs-target="#casinoCollapse" aria-expanded="false">
                                <span>CASSINO</span>
                                <span class="caret"></span>
                            </button>
                            <div class="collapse" id="casinoCollapse">
                                <ul class="list-unstyled ps-3 mt-2 mb-0">
                                    @foreach(\App\Models\GameExclusive::limit(10)->orderBy('views','desc')->where('active',1)->get() as $gameExclusive)
                                        <li>
                                            <a href="{{ route('web.vgames.show', ['game' => $gameExclusive->uuid]) }}" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none">
                                                <img src="{{ asset('storage/'.$gameExclusive->icon) }}" width="20" class="me-2" alt=""> {{ $gameExclusive->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(\App\Models\Category::count() > 0)
                        <li class="mt-3">
                            <button class="btn btn-toggle w-100 text-start d-flex justify-content-between align-items-center px-2" data-bs-toggle="collapse" data-bs-target="#catsCollapse" aria-expanded="false">
                                <span>CATEGORIAS</span>
                                <span class="caret"></span>
                            </button>
                            <div class="collapse" id="catsCollapse">
                                <ul class="list-unstyled ps-3 mt-2 mb-0">
                                    @foreach(\App\Models\Category::all() as $category)
                                        <li>
                                            <a href="{{ route('web.category.index', ['slug' => $category->slug]) }}" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none">{{ $category->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
</div>
