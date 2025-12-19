@extends('layouts.web')

@push('styles')
<style>
    .game-card{ background: #fff; border-radius: .6rem; overflow: hidden; box-shadow: 0 6px 18px rgba(15,23,42,0.06); transition: transform .18s ease, box-shadow .18s ease; }
    .game-card:hover{ transform: translateY(-6px); box-shadow: 0 14px 30px rgba(15,23,42,0.12); }
    .game-thumb{ width:100%; height:140px; background-size:cover; background-position:center; }
    .game-title{ font-size: .95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    @media (max-width: 576px){ .game-thumb{ height:110px; } }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        @include('includes.navbar_top')
        @include('includes.navbar_left')

        <div class="page__content">

            <div class="row">
                <div class="col-lg-6">
                    <h2>Todos os jogos</h2>
                </div>
                <div class="col-lg-6">

                    <form action="{{ route('web.game.list') }}" class="w-full" method="GET">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" name="searchTerm" class="form-control" placeholder="Digite o nome do jogo" value="{{ $search }}">

                            <span class="input-group-text" style="padding-right: 5px;">
                                <button type="submit" class="px-4">
                                    Buscar
                                </button>
                            </span>
                        </div>
                        @foreach(request()->except(['searchTerm', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                    </form>

                </div>
            </div>

                <div class="row">
                    @php
                        // helper to get image/url per game type
                        function gameImage($game, $tab){
                            if($tab == 'fivers') return (str_starts_with($game->banner, 'http') ? $game->banner : asset('storage/'.$game->banner));
                            if($tab == 'exclusives') return (str_starts_with($game->cover, 'http') ? $game->cover : asset('storage/'.$game->cover));
                            if($tab == 'vibra') return (str_starts_with($game->game_cover, 'http') ? $game->game_cover : asset('storage/'.$game->game_cover));
                            // default/provider
                            return (str_starts_with($game->image ?? '', 'http') ? ($game->image ?? '') : asset('storage/'.($game->image ?? '')));
                        }
                    @endphp

                    @foreach($games as $game)
                        @php
                            // determine link depending on available attributes
                            if(isset($game->slug)) {
                                $link = route('web.game.index', ['slug' => $game->slug]);
                            } elseif(isset($game->uuid)) {
                                $link = route('web.vgames.show', ['game' => $game->uuid]);
                            } elseif(isset($game->game_id)) {
                                $link = route('web.vibragames.show', ['id' => $game->game_id]);
                            } elseif(isset($game->game_code)) {
                                $link = route('web.fivers.show', ['code' => $game->game_code]);
                            } else {
                                $link = '#';
                            }

                            $img = gameImage($game, $tab);
                            $title = $game->name ?? ($game->game_name ?? ($game->game_title ?? 'Jogo'));
                            $provider = $game->provider ?? $game->provider_service ?? '';
                        @endphp

                        <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-4">
                            <div class="game-card">
                                <div class="game-thumb" style="background-image: url('{{ $img }}')"></div>
                                <div class="game-info p-2">
                                    <h6 class="game-title mb-1">{{ $title }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ $provider }}</small>
                                        <a href="{{ $link }}" class="btn btn-sm btn-success">Jogar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $games->links() }}
                </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush
