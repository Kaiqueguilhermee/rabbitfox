# Controle de Acesso WebView — rabbitfox.shop

## Objetivo
Permitir que apenas o app WebView acesse o conteúdo real do projeto, enquanto navegadores e bots recebem sempre o jogo público localizado em `/rabbitAmoung`.

---

## 1. Emissão de Token

- **Endpoint:**
  - `POST https://rabbitfox.shop/api/app/token`
- **Request:**
  - Envie credenciais do app (exemplo: `client_id`, `client_secret`).
  - Exemplo:
    ```json
    {
      "client_id": "APP_WEBVIEW",
      "client_secret": "SEGREDO"
    }
    ```
- **Response:**
  ```json
  {
    "token": "seu-token-gerado",
    "expires_in": 3600
  }
  ```

---

## 2. Middleware de Verificação

- **Verifica:**
  - Header `X-App-Token` ou `Authorization: Bearer <token>`, query param ou cookie.
  - Se token ausente/inválido/expirado, redireciona para `/rabbitAmoung`.
  - Se User-Agent de bot/navegador, também redireciona para `/rabbitAmoung`.
- **Exclusão:**
  - Middleware ignora requisições para `/rabbitAmoung` e assets públicos.

---

## 3. Fluxo WebView

1. WebView solicita token ao endpoint.
2. Armazena token.
3. Envia token em todas as requisições protegidas (header).
4. Se receber redirect para `/rabbitAmoung`, solicita novo token ou mostra jogo público.

---

## 4. Fluxo Navegador/Bot

- Qualquer acesso sem token ou por bot/navegador → sempre redirecionado para `/rabbitAmoung`.

---

## 5. Estrutura de Pastas

- Projeto real: `/` (acesso protegido por token).
- Jogo público: `/rabbitAmoung` (acesso livre).

---

## 6. Segurança

- Proteja endpoint de emissão de token.
- Use HTTPS (`https://rabbitfox.shop/`).
- TTL curto para tokens (configurável).

---

## 7. Exemplos de Requisição

- **Solicitar token:**
  ```http
  POST https://rabbitfox.shop/api/app/token
  Content-Type: application/json
  Body: { "client_id": "APP_WEBVIEW", "client_secret": "SEGREDO" }
  ```
- **Acessar conteúdo protegido:**
  ```http
  GET https://rabbitfox.shop/
  X-App-Token: <token>
  ```
- **Fallback público:**
  ```http
  GET https://rabbitfox.shop/rabbitAmoung
  ```

---

## 8. Troubleshooting

- 404 em assets: verifique se pasta pública existe e URL corresponde.
- Redirecionamento inesperado: revise exclusões do middleware e headers enviados.
- Use sempre HTTPS.

---

## 9. Exemplo de Integração WebView (JavaScript)

```javascript
// 1) solicitar token
const resp = await fetch('https://rabbitfox.shop/api/app/token', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ client_id: 'APP_WEBVIEW', client_secret: 'SEGREDO' })
});
const data = await resp.json();
const token = data.token;

// 2) acessar rota protegida
fetch('https://rabbitfox.shop/game/some-slug', {
  headers: { 'X-App-Token': token }
}).then(r => {
  if (r.redirected) window.location = r.url;
  // ou tratar resposta normalmente
});
```

---

## 10. Checklist para Implantação

- [ ] Pasta pública `/rabbitAmoung` existe e está acessível.
- [ ] Middleware exclui `/rabbitAmoung` e assets.
- [ ] Endpoint de token protegido.
- [ ] WebView envia token via header.
- [ ] Testes com e sem token confirmam redirecionamento correto.
- [ ] HTTPS ativo.

---

## Observações Adicionais para o Backend

- **Detecção de Bots:**
  Implemente uma lista abrangente de User-Agents de bots/crawlers, incluindo variações e mantenha-a atualizada para bloquear novos robôs.

- **Validação Robusta de Token:**
  - Nunca permita acesso ao conteúdo real com token ausente, inválido ou expirado.
  - Sempre valide a expiração do token antes de liberar qualquer recurso protegido.
  - Implemente renovação segura de token, invalidando tokens antigos.

- **Proteção do Endpoint de Token:**
  - O endpoint `/api/app/token` deve ser acessível apenas pelo app oficial.
  - Use HTTPS obrigatório.
  - Considere autenticação mútua ou IP restrito, se possível.

- **Redirecionamento Seguro:**
  - O redirecionamento para `/rabbitAmoung` deve ocorrer antes de carregar qualquer dado sensível.
  - Nunca exponha dados do app antes da validação do token.

- **Logs e Monitoramento:**
  - Registre tentativas de acesso inválidas, tokens errados, User-Agents suspeitos e abuse.
  - Monitore padrões de acesso para detectar ataques ou uso indevido.

- **Exclusão de Assets Públicos:**
  - Garanta que assets estáticos (CSS, JS, imagens, áudios) e o jogo público estejam sempre acessíveis, mesmo sem token.
  - Implemente as exclusões no backend/middleware.

- **Consistência nos Headers:**
  - Padronize o uso de `X-App-Token` ou `Authorization: Bearer` para validação.
  - Evite depender de query params, exceto em último caso.

---

Dúvidas ou integração específica (Android/iOS)? Solicite exemplos de código para WebView!