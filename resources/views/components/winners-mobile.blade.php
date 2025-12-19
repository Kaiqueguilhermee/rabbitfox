@php
    $topGames = $topTrendGames ?? [];
    $avatars = range(1, 40);
    $periods = [
        'hoje' => [
            'names' => ['Jefferson F***', 'Wu Z***', 'Celso L***', 'Marco S***', 'Jose M***', 'Cintia A***', 'Elayne C***', 'Matheus C***'],
            'min' => 600, 'max' => 3000
        ],
        '3dias' => [
            'names' => ['Patricia G***', 'Bruno H***', 'Camila N***', 'Diego V***', 'Lucas S***', 'Fernanda A***', 'Rafael D***', 'Juliana R***'],
            'min' => 3000, 'max' => 5000
        ],
        '7dias' => [
            'names' => ['Amanda P***', 'Thiago M***', 'Gabriel F***', 'Larissa C***', 'Vinicius L***', 'Beatriz S***', 'Henrique T***', 'Sofia N***'],
            'min' => 5000, 'max' => 8000
        ],
        '15dias' => [
            'names' => ['Eduardo V***', 'Marina D***', 'Felipe R***', 'Aline B***', 'Gustavo H***', 'Isabela K***', 'Renato J***', 'Paula Q***'],
            'min' => 8000, 'max' => 12000
        ],
        '30dias' => [
            'names' => ['Otavio Z***', 'Simone W***', 'Danilo X***', 'Helena Y***', 'Ricardo U***', 'Tatiane I***', 'Murilo O***', 'Leticia P***'],
            'min' => 12000, 'max' => 30000
        ],
    ];
    $topGamesArr = is_array($topGames) ? $topGames : $topGames->all();
    $periodGames = [
        'hoje' => count($topGamesArr) >= 8 ? array_slice($topGamesArr, 0, 8) : $topGamesArr,
        '3dias' => count($topGamesArr) >= 16 ? array_slice($topGamesArr, 8, 8) : $topGamesArr,
        '7dias' => count($topGamesArr) >= 24 ? array_slice($topGamesArr, 16, 8) : $topGamesArr,
        '15dias' => count($topGamesArr) >= 32 ? array_slice($topGamesArr, 24, 8) : $topGamesArr,
        '30dias' => count($topGamesArr) >= 40 ? array_slice($topGamesArr, 32, 8) : $topGamesArr,
    ];
@endphp
<script>
function setTopWinnersPeriod(period) {
    document.querySelectorAll('.top-winners-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector('.top-winners-tab[data-period="'+period+'"').classList.add('active');
    document.querySelectorAll('.top-winners-track').forEach(track => {
        track.style.display = 'none';
    });
    document.getElementById('top-winners-track-'+period).style.display = 'flex';
}
</script>

<div class="top-winners-container-mobile">
    <!-- Tabs de Período -->
    <div class="top-winners-tabs-container">
        <div class="top-winners-tabs-text">
            <img src="https://cdn.7games.bet.br/react-app/cms/images/icons/clock-two_home.svg" alt="Clock">
            <p>Período:</p>
        </div>
        <div class="top-winners-tabs">
            <div class="top-winners-tab active" data-period="hoje" onclick="setTopWinnersPeriod('hoje')">Hoje</div>
            <div class="top-winners-tab" data-period="3dias" onclick="setTopWinnersPeriod('3dias')">3 Dias</div>
            <div class="top-winners-tab" data-period="7dias" onclick="setTopWinnersPeriod('7dias')">7 Dias</div>
            <div class="top-winners-tab" data-period="15dias" onclick="setTopWinnersPeriod('15dias')">15 Dias</div>
            <div class="top-winners-tab" data-period="30dias" onclick="setTopWinnersPeriod('30dias')">30 Dias</div>
        </div>
    </div>

    <!-- Lista de Ganhadores com Scroll + Top Ganhos à direita -->
    <div class="top-winners-row-flex">
        <div class="top-winners-header side">
            <img src="https://cdn.7games.bet.br/content/assets/trofeu2.png?q=0&lossless=1&h=32&w=32" alt="Troféu">
            <p>Top Ganhos</p>
        </div>
        <div class="top-winners-scroll">
            @foreach(['hoje','3dias','7dias','15dias','30dias'] as $idx => $period)
                <div class="top-winners-track" id="top-winners-track-{{ $period }}" style="display: {{ $period == 'hoje' ? 'flex' : 'none' }};">
                    @php
                        $names = $periods[$period]['names'];
                        $min = $periods[$period]['min'];
                        $max = $periods[$period]['max'];
                        $games = $periodGames[$period] ?? $topGames;
                        $count = 8;
                        $mockWinners = [];
                        for ($i = 0; $i < $count; $i++) {
                            $name = $names[array_rand($names)];
                            $game = $games[array_rand($games)];
                            $mockWinners[] = [
                                'name' => $name,
                                'game' => $game->name,
                                'amount' => 'R$ ' . number_format(rand($min, $max), 2, ',', '.'),
                                'image' => str_starts_with($game->image, 'http') ? $game->image : asset('storage/'.$game->image),
                                'avatar' => $avatars[array_rand($avatars)]
                            ];
                        }
                    @endphp
                    @foreach($mockWinners as $winner)
                        <div class="top-winner-card">
                            <div class="top-winner-game-img">
                                <img src="{{ $winner['image'] }}" alt="{{ $winner['game'] }}">
                            </div>
                            <div class="top-winner-info">
                                <div class="top-winner-player">
                                    <div class="top-winner-avatar">
                                        <img class="avatar-roll" src="https://cdn.7games.bet.br/content/assets/rodela.svg">
                                        <img class="avatar-img" src="https://cdn.7games.bet/content/images/avatars/v2/{{ $winner['avatar'] }}.webp">
                                    </div>
                                    <p class="player-name">{{ $winner['name'] }}</p>
                                </div>
                                <span class="game-name">{{ $winner['game'] }}</span>
                                <p class="winner-amount">{{ $winner['amount'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
