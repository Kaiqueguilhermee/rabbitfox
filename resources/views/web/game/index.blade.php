@extends('layouts.web')

@push('styles')
<style>
    /* Esconder navegação mobile quando jogo está aberto */
    .mobile-bottom-nav {
        display: none !important;
    }
    
    /* Garantir que o jogo ocupe toda a tela */
    body, html {
        overflow: hidden;
        margin: 0;
        padding: 0;
        height: 100%;
    }
    
    .playgame {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        z-index: 9999;
    }
    
    .playgame-body {
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .game-full {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Botão de voltar */
    .game-back-btn {
        position: fixed;
        top: 10px;
        left: 10px;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }
    
    .game-back-btn:hover {
        background: rgba(0, 0, 0, 0.95);
        transform: scale(1.05);
        color: white;
        text-decoration: none;
    }
    
    .game-back-btn i {
        font-size: 18px;
    }
    
    /* Esconder header/footer/navbar */
    header, footer, nav:not(.mobile-bottom-nav) {
        display: none !important;
    }
</style>
@endpush

@section('content')
   <div class="playgame">
       <a href="{{ url('/') }}" class="game-back-btn">
           <i class="fas fa-arrow-left"></i>
           <span>Voltar</span>
       </a>
       <div class="playgame-body">
           <iframe src="{{ $gameUrl }}" class="game-full"></iframe>
       </div>
   </div>
@endsection
