# Configuração do Webhook Drakon

## URLs do Webhook

Após subir na VPS, configure uma destas URLs no painel da Drakon:

```
https://seu-dominio.com/api/drakon_api
https://seu-dominio.com/api/webhook/drakon
https://seu-dominio.com/api/drakon/webhook
```

## Métodos Implementados

### 1. account_details
Retorna informações da conta do usuário.

**Request:**
```json
{
  "method": "account_details",
  "user_id": "1"
}
```

**Response Success:**
```json
{
  "email": "usuario@exemplo.com",
  "name_jogador": "João Silva",
  "date": "2025-01-01T12:00:00Z"
}
```

**Response Error:**
```json
{
  "status": false,
  "error": "INVALID_USER"
}
```

---

### 2. user_balance
Retorna o saldo da carteira ativa do usuário.

**Request:**
```json
{
  "method": "user_balance",
  "user_id": "1"
}
```

**Response Success:**
```json
{
  "status": 1,
  "balance": 1500.75
}
```

**Response Error:**
```json
{
  "status": 0,
  "error": "INVALID_USER"
}
```

---

### 3. transaction_bet
Registra uma aposta e debita do saldo.

**Request:**
```json
{
  "method": "transaction_bet",
  "user_id": "1",
  "transaction_id": "txn_12345",
  "bet": 50.00,
  "round_id": "round_67890",
  "game": "cyber-ninja"
}
```

**Response Success:**
```json
{
  "status": true,
  "balance": 1450.75
}
```

**Response Error:**
```json
{
  "status": false,
  "error": "NO_BALANCE"
}
```

**Outros erros:**
- `INVALID_PARAMS`: Parâmetros faltando ou inválidos
- `INVALID_USER`: Usuário não encontrado ou carteira inativa

---

### 4. transaction_win
Registra um ganho e credita no saldo.

**Request:**
```json
{
  "method": "transaction_win",
  "user_id": "1",
  "transaction_id": "txn_12346",
  "win": 100.00,
  "round_id": "round_67890",
  "game": "cyber-ninja"
}
```

**Response Success:**
```json
{
  "status": true,
  "balance": 1550.75
}
```

**Response Error:**
```json
{
  "status": false,
  "error": "NO_AMOUNT"
}
```

---

### 5. refund
Reverte uma transação (bet ou win).

**Request:**
```json
{
  "method": "refund",
  "user_id": "1",
  "transaction_id": "txn_12345",
  "amount": 50.00,
  "round_id": "round_67890",
  "game": "cyber-ninja"
}
```

**Response Success:**
```json
{
  "status": true,
  "balance": 1500.75
}
```

**Response Error:**
```json
{
  "status": false,
  "error": "INVALID_TRANSACTION"
}
```

---

### 6. cancel
Cancela uma transação previamente registrada.

**Request:**
```json
{
  "method": "cancel",
  "user_id": "1",
  "transaction_id": "txn_12345",
  "round_id": "round_67890",
  "game": "cyber-ninja",
  "amount": 50.00
}
```

**Response Success:**
```json
{
  "status": true,
  "transaction_status": "CANCELED"
}
```

**Response Error:**
```json
{
  "status": false,
  "error": "INVALID_TRANSACTION"
}
```

---

## Códigos de Erro

| Código | Descrição |
|--------|-----------|
| `INVALID_METHOD` | Método não reconhecido |
| `INVALID_PARAMS` | Parâmetros obrigatórios faltando |
| `INVALID_USER` | Usuário não encontrado ou carteira inativa |
| `INVALID_TRANSACTION` | Transação não encontrada |
| `NO_BALANCE` | Saldo insuficiente |
| `NO_AMOUNT` | Valor inválido ou negativo |
| `INTERNAL_ERROR` | Erro interno do servidor |

---

## Logs

Todos os webhooks são registrados em `storage/logs/laravel.log` com:
- Método recebido
- Dados completos
- IP de origem
- Resultado do processamento

Exemplo:
```
[2025-12-08 18:30:00] local.INFO: ========== DRAKON WEBHOOK RECEIVED ==========
[2025-12-08 18:30:00] local.INFO: Method: transaction_bet
[2025-12-08 18:30:00] local.INFO: Data: {...}
[2025-12-08 18:30:00] local.INFO: IP: 192.168.1.100
[2025-12-08 18:30:00] local.INFO: =============================================
[2025-12-08 18:30:00] local.INFO: Drakon BET processed {...}
```

---

## Testes Locais

Para testar localmente, use um túnel como ngrok:

```bash
ngrok http 80
```

Isso gerará uma URL pública como `https://abc123.ngrok.io` que você pode configurar temporariamente no painel Drakon.

---

## Segurança

### Recomendações para produção:

1. **Validação de IP**: Adicione whitelist dos IPs da Drakon
2. **Hash de validação**: Implemente verificação de hash/signature se a Drakon fornecer
3. **Rate limiting**: Configure limite de requisições no nginx/cloudflare
4. **HTTPS**: Sempre use conexão segura (já configurado no Laravel)

### Exemplo de validação de IP (adicionar no webhook):

```php
$allowedIps = ['IP_DRAKON_1', 'IP_DRAKON_2'];
if (!in_array($request->ip(), $allowedIps)) {
    return response()->json(['status' => false, 'error' => 'UNAUTHORIZED'], 403);
}
```

---

## Checklist de Deploy na VPS

- [ ] Servidor configurado com PHP 8.2+
- [ ] SSL/HTTPS ativo
- [ ] Firewall permitindo requisições da Drakon
- [ ] URL do webhook configurada no painel Drakon
- [ ] Logs sendo monitorados em `storage/logs/laravel.log`
- [ ] Teste de cada método do webhook
- [ ] Validação de saldo antes/depois das transações

---

## Suporte Drakon

- **Telegram**: @drakongatorsupport
- **Documentação**: Forneça a URL do webhook e solicite testes
- **Problema atual**: API retorna JSON truncado (~460 bytes) na resposta do game_launch
