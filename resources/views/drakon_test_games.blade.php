<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Drakon - Teste Jogos</title>
</head>
<body>
  <h1>Drakon - Teste: obter jogos</h1>
  <div id="status">Buscando jogos...</div>
  <ul id="games-list"></ul>
  <script>
    const CONFIG = {!! json_encode([
        'agent_code' => $agent_code ?? '',
        'agent_token' => $agent_token ?? '',
        'user_id' => optional($user)->id ?? '',
        'user_name' => optional($user)->name ?? '',
    ]) !!};

    async function fetchGames() {
      try {
        const res = await fetch('/api/games/all?include_inactive=1');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();
        console.log('Drakon - jogos recebidos:', data);

        const list = document.getElementById('games-list');
        list.innerHTML = '';
        if (Array.isArray(data) && data.length) {
          data.forEach(g => {
            const li = document.createElement('li');
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerText = `${g.game_name} — ${g.provider_game}`;
            btn.style.cursor = 'pointer';
            btn.addEventListener('click', () => launchGame(g));
            li.appendChild(btn);
            list.appendChild(li);
          });
          document.getElementById('status').innerText = 'Jogos recebidos e listados abaixo.';
        } else {
          document.getElementById('status').innerText = 'Nenhum jogo retornado.';
        }
      } catch (e) {
        console.error('Drakon - erro ao buscar jogos:', e);
        document.getElementById('status').innerText = 'Erro ao buscar jogos. Veja console (F12).';
      }
    }

    async function launchGame(game) {
      try {
        document.getElementById('status').innerText = 'Lançando jogo: ' + game.game_name;

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const res = await fetch('/drakon-launch', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ game_id: game.game_code })
        });

        const data = await res.json();

        if (res.status === 201 && data.game_url) {
          console.log('Drakon - game_url (server):', data.game_url);
          window.location.href = data.game_url;
        } else {
          console.error('Drakon - erro ao lançar jogo (server):', data);
          alert('Erro ao lançar jogo: ' + (data.message || JSON.stringify(data)));
          document.getElementById('status').innerText = 'Erro ao lançar jogo. Veja console.';
        }
      } catch (e) {
        console.error('Drakon - exceção ao lançar jogo (server):', e);
        alert('Erro ao lançar jogo. Veja console.');
        document.getElementById('status').innerText = 'Erro ao lançar jogo. Veja console.';
      }
    }

    // Inicializa
    fetchGames();
  </script>
</body>
</html>
