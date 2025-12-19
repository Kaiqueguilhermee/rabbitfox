@extends('layouts.web')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="background: #f8fafc;">
    <div class="card shadow-lg border-0 p-5" style="max-width: 400px; width: 100%; background: #fff; border-radius: 1.5rem;">
        <div class="text-center">
            <img src="{{ asset('assets/images/rollover-blocked.svg') }}" alt="Rollover" style="width: 80px; margin-bottom: 1.5rem;">
            <h3 class="mb-3" style="color: #1a1c1f;">Saque Bloqueado</h3>
            <p class="mb-4" style="color: #555;">Você precisa apostar mais <strong>R$ {{ number_format(auth()->user()->wallet->balance_bonus_rollover, 2, ',', '.') }}</strong> para liberar o saque.</p>
            <a href="/" class="btn btn-primary w-100" style="border-radius: 2rem; font-weight: 600;">JOGAR AGORA</a>
        </div>
    </div>
</div>
@endsection
