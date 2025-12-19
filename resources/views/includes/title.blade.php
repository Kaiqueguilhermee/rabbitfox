<div class="header-box">
    <div class="header-title {{ $title == '+Jogados Da Semana' ? 'mobile-paid-today' : '' }}">
        @if(isset($icon) && $icon)
            @if($title == '+Jogados Da Semana')
                <img src="https://cdn.7games.bet.br/content/assets/icons/real-money.png?q=0&lossless=1&h=20&w=20" alt="Moeda" style="width: 23px; height: 23px; margin-right: 10px;">
            @else
                <i class="{{ $icon }}" style="font-size: 23px;margin-right: 10px;color: #65cb24;"></i>
            @endif
        @endif
        <h4 class="section-title-text">
            @if($title == '+Jogados Da Semana')
                <span class="hidden md:inline">{{ $title }}</span>
                <span class="md:hidden">MAIS PAGOU HOJE</span>
            @else
                {{ $title }}
            @endif
        </h4>
    </div>
    <div>
        <a href="{{ $link }}">@if(isset($labelLink)) {{ $labelLink }} @else Ver todos @endif <i class="fa-regular fa-chevron-right" style="font-size: 18px;margin-left: 10px;color: #65cb24;"></i></a>
    </div>
</div>
