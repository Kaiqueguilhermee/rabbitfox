@extends('layouts.web')

@push('styles')

@endpush

@section('content')
    <div class="container-fluid">
        @include('includes.navbar_top')
        @include('includes.navbar_left')

        <div class="page__content">
            <br>

            <div class="row">
                <div class="col-lg-6">
                    <h2>{{ $category->name }}</h2>
                </div>
                <div class="col-lg-6"></div>
            </div>

            <div class="row mt-3">
                @foreach($games as $game)
                    <div class="col-xl-2 col-lg-2 col-md-3 col-6 caixa-loop-elementos">
                        <a href="{{ route('web.game.index', ['slug' => $game->slug]) }}" class="inner-loop-elementos">
                            <img src="{{ str_starts_with($game->image, 'http') ? $game->image : asset('storage/'.$game->image) }}" alt="{{ $game->name }}" class="img-fluid rounded-3" style="width: 100%; height: auto; object-fit: cover;">
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 mb-5">
                <div class="col-lg-6"></div>
                <div class="col-lg-6">
                    {{ $games->links() }}
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush
