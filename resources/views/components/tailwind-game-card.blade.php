<!-- Exemplo de card de jogo 100% Tailwind, pronto para rolagem mobile -->
<a href="#" class="block rounded-lg overflow-hidden shadow-lg bg-gray-800 hover:bg-gray-700 transition-colors duration-200 touch-pan-y">
  <img src="/assets/images/exemplo-jogo.jpg" alt="Nome do Jogo" class="w-full h-40 object-cover select-none pointer-events-auto" draggable="false">
  <div class="p-4">
    <h3 class="text-white text-lg font-bold mb-2 truncate">Nome do Jogo</h3>
    <p class="text-gray-400 text-sm">Categoria ou descrição curta</p>
  </div>
</a>
<!--
Dicas:
- Use touch-pan-y do Tailwind para garantir rolagem vertical.
- pointer-events-auto e select-none evitam bloqueios de eventos.
- overflow-hidden/rounded-lg deixam o card bonito e responsivo.
- Para grid: use grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4
-->
