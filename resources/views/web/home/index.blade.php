@extends('layouts.web')

@section('title', config('setting')['software_name'].' - Cassino Online | Jogos de Slot e Apostas em Futebol')

@section('seo')
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta name="description" content="Bem-vindo à {{ config('setting')['software_name'] }} - o melhor cassino online com uma ampla seleção de jogos de slot, apostas em jogos de futebol e uma experiência de aposta fácil e divertida. Jogue Fortune Tiger, Fortune OX e muito mais!">
    <meta name="keywords" content="{{ config('setting')['software_name'] }}, cassino online, jogos de slot, apostas em futebol, Fortune Tiger, Fortune OX">

    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ config('setting')['software_name'] }} - Apostas Online | Jogos de Slot e Apostas em Futebol" />
    <meta property="og:description" content="Bem-vindo à {{ config('setting')['software_name'] }} - o melhor cassino online com uma ampla seleção de jogos de slot, apostas em jogos de futebol e uma experiência de aposta fácil e divertida. Jogue Fortune Tiger, Fortune OX e muito mais!" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="{{ config('setting')['software_name'] }} - Apostas Online | Jogos de Slot e Apostas em Futebol" />
    <meta property="og:image" content="{{ asset('/assets/images/banner-1.png') }}" />
    <meta property="og:image:secure_url" content="{{ asset('/assets/images/banner-1.png') }}" />
    <meta property="og:image:width" content="1024" />
    <meta property="og:image:height" content="571" />

    <meta name="twitter:title" content="{{ config('setting')['software_name'] }} - Apostas Online | Jogos de Slot e Apostas em Futebol">
    <meta name="twitter:description" content="Bem-vindo à {{ config('setting')['software_name'] }} - o melhor cassino online com uma ampla seleção de jogos de slot, apostas em jogos de futebol e uma experiência de aposta fácil e divertida. Jogue Fortune Tiger, Fortune OX e muito mais!">
    <meta name="twitter:image" content="{{ asset('/assets/images/banner-1.png') }}"> <!-- Substitua pelo link da imagem que deseja exibir -->
    <meta name="twitter:url" content="{{ url('/') }}"> <!-- Substitua pelo link da sua página -->
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/splide-core.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        <div class="">
            @include('includes.navbar_top')
            @include('includes.navbar_left')

            <div class="page__content">
                <section id="image-carousel" class="splide hidden md:block" aria-label="">
                    <div class="splide__track">
                        <div class="splide-banner">
                            Ganhe 10 rodadas grátis <span style="margin-left: 10px"><i class="fa-solid fa-fire"></i></span>
                        </div>
                        <ul class="splide__list">
                            @foreach(\App\Models\Banner::where('type', 'carousel')->get() as $banner)
                                <li class="splide__slide">
                                    <a href="{{ $banner->link }}">
                                        <img src="{{ asset('storage/'.$banner->image) }}" alt="">
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>

                <!-- Search -->
                 <br>
                <form action="{{ url('/') }}" method="GET" class="mb-6">
                    <div class="relative">
                        <input id="home-search-input" type="text" name="search" value="{{ request('search') }}" class="search-input w-full pl-4 pr-12" placeholder="Digite o que você procura..." aria-label="Pesquisar">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-primary text-xl bg-transparent border-0 p-0 m-0" style="background: none; border: none;">
                            <i class="fa-duotone fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <!-- Ganhadores Mobile -->
                <div class="block md:hidden mb-4">
                    @include('components.winners-mobile')
                </div>

                <!-- Jogos da plataforma -->
                @if(count($gamesExclusives) > 0)
                    <div class="mt-8">
                        @include('includes.title', ['link' => url('/games?tab=exclusives'), 'title' => 'Jogos da Casa'])
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4">
                        @foreach(\App\Models\Banner::where('type', 'home')->take(6)->get() as $banner)
                            @component('components.tailwind-game-card')
                                @slot('href', $banner->link)
                                @slot('img', asset('storage/'.$banner->image))
                                @slot('title', 'Banner')
                                @slot('desc', '')
                            @endcomponent
                        @endforeach
                    </div>

                    <div class="d-steam-cards js-steamCards">
                        @foreach($gamesExclusives->take(6) as $gamee)
                            <a href="{{ route('web.vgames.show', ['game' => $gamee->uuid]) }}" class="d-steam-card-wrapper">
                                <div class="d-steam-card js-steamCard" style="background-image: url('{{ asset('storage/'.$gamee->cover) }}')"></div>
                            </a>
                        @endforeach
                    </div>
                @endif


                <!-- TopTrend Gaming - Jogos em Destaque -->
                @if(isset($topTrendGames) && count($topTrendGames) > 0)
                    @include('includes.title', ['link' => url('/games?tab=all'), 'title' => 'Mais pagou Hoje'])
                    <div class="grid grid-cols-3 gap-3 mt-4 sm:grid-cols-4 lg:grid-cols-6">
                        @foreach($topTrendGames->take(6) as $game)
                            <a href="{{ route('web.play', ['uuid' => $game->uuid]) }}" class="game-card">
                                <img src="{{ str_starts_with($game->image, 'http') ? $game->image : asset('storage/'.$game->image) }}" alt="{{ $game->name }}" class="game-card-image">
                            </a>
                        @endforeach
                    </div>
                    <br>
                @endif
                {{-- Categorias marcadas para home --}}
                @if(isset($categoriesHome) && count($categoriesHome) > 0)
                    @foreach($categoriesHome->take(6) as $category)
                        @if($category->gamesSlotgrator->count() > 0)
                            <div >
                                @include('includes.title', ['link' => url('/category/'.$category->slug), 'title' => $category->name])
                            </div>
                            <div class="grid grid-cols-3 gap-3 mt-4 sm:grid-cols-4 lg:grid-cols-6">
                                @foreach($category->gamesSlotgrator->take(6) as $game)
                                    <a href="{{ route('web.play', ['uuid' => $game->uuid]) }}" class="game-card">
                                        <img src="{{ str_starts_with($game->image, 'http') ? $game->image : asset('storage/'.$game->image) }}" alt="{{ $game->name }}" class="game-card-image">
                                    </a>
                                @endforeach
                            </div>
                                <br>
                        @endif
                    @endforeach
                @endif
                @if(count($providers) > 0)
                    @foreach($providers as $provider)
                        @if($provider->games->where('status', 1)->count() > 0)
                            @include('includes.title', ['link' => url('/games?provider='.$provider->code.'&tab=fivers'), 'title' => $provider->name])

                            <div class="grid grid-cols-3 gap-3 mt-4 sm:grid-cols-4 lg:grid-cols-6">
                                @foreach($provider->games->where('status', 1)->take(6) as $gameProvider)
                                    <a href="{{ route('web.fivers.show', ['code' => $gameProvider->game_code]) }}" class="game-card">
                                        <img src="{{ asset('storage/'.$gameProvider->banner) }}" alt="{{ $gameProvider->game_name }}" class="game-card-image">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                @endif
                
                <!-- Slotegrator -->
                @if(count($games) > 0)
                    @include('includes.title', ['link' => url('/games?tab=all'), 'title' => 'Todos os Jogos'])

                    <div class="grid grid-cols-3 gap-3 mt-4 sm:grid-cols-4 lg:grid-cols-6">
                        @foreach($games->take(15) as $game)
                            @php
                                $service = strtolower($game->provider_service ?? $game->provider ?? '');
                                $isDrakon = $service === 'drakon';
                            @endphp

                            @if($isDrakon)
                                <a href="{{ route('web.play', ['uuid' => $game->uuid]) }}" class="game-card">
                                    <img src="{{ str_starts_with($game->image, 'http') ? $game->image : asset('storage/'.$game->image) }}" alt="{{ $game->name }}" class="game-card-image">
                                </a>
                            @else
                                <a href="{{ route('web.game.index', ['slug' => $game->slug]) }}" class="game-card">
                                    <img src="{{ asset('storage/'.$game->image) }}" alt="{{ $game->name }}" class="game-card-image">
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if(count($gamesVibra) > 0)
                    @include('includes.title', ['link' => url('/games?tab=vibra'), 'title' => 'Jogos Vibra'])

                    <div class="grid grid-cols-3 gap-3 mt-4 sm:grid-cols-4 lg:grid-cols-6">
                        @foreach($gamesVibra->take(6) as $vibra)
                            <a href="{{ route('web.vibragames.show', ['id' => $vibra->game_id]) }}" class="game-card">
                                <img src="{{ asset('storage/'.$vibra->game_cover) }}" alt="{{ $vibra->name }}" class="game-card-image">
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5">
                    @include('includes.title', ['link' => url('como-funciona'), 'title' => 'F.A.Q', 'icon' => 'fa-light fa-circle-info', 'labelLink' => 'Saiba mais'])
                </div>

                @include('web.home.sections.faq')

                @include('includes.footer')

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/splide.min.js') }}"></script>
    <script>
        document.addEventListener( 'DOMContentLoaded', function () {
            var elemento = document.getElementById('splide-soccer');

            if (elemento) {
                new Splide( '#splide-soccer', {
                    type   : 'loop',
                    drag   : 'free',
                    focus  : 'center',
                    autoplay: 'play',
                    perPage: 3,
                    arrows: false,
                    pagination: false,
                    breakpoints: {
                        640: {
                            perPage: 1,
                        },
                    },
                    autoScroll: {
                        speed: 1,
                    },
                }).mount();
            }

            new Splide( '#image-carousel', {
                arrows: false,
                pagination: false,
                type    : 'loop',
                autoplay: 'play',
            }).mount();
        } );
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('home-search-input');
            if (!input) return;

            var messages = [
                'Digite o que você procura...',
                'Experimente Fortune Tiger',
                'Tente a sorte em Fortune OX',
                'Procure por jogos, bônus ou provedores'
            ];
            var idx = 0;
            var rotInterval = 3000;
            var rotTimer = null;

            function setPlaceholder() {
                if (document.activeElement === input) return;
                if (input.value && input.value.trim().length > 0) return;
                input.setAttribute('placeholder', messages[idx]);
                idx = (idx + 1) % messages.length;
            }

            function startRotator() {
                if (rotTimer) return;
                rotTimer = setInterval(setPlaceholder, rotInterval);
            }

            function stopRotator() {
                if (!rotTimer) return;
                clearInterval(rotTimer);
                rotTimer = null;
            }

            // Start
            setPlaceholder();
            startRotator();

            input.addEventListener('focus', function() {
                stopRotator();
            });

            input.addEventListener('blur', function() {
                if (!input.value || input.value.trim().length === 0) {
                    setPlaceholder();
                    startRotator();
                }
            });
        });
    </script>
@endpush
