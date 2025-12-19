<!-- Bootstrap Offcanvas Sidebar -->
<div>


    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSidebarLabel"</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="h-full px-3 py-4 overflow-y-auto">
                <ul class="list-unstyled ps-2 mb-0">
                    <li class="mb-3">
                        <a href="" class="d-flex align-items-center gap-2 px-2 py-1 rounded text-decoration-none btn btn-success text-white font-bold btn-gains text-center w-100">
                            <i class="fa-light fa-rocket-launch"></i>
                            <span class="text-center">Ganhe R$ 50 grátis</span>
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
