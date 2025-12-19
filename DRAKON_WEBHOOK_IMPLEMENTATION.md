# Drakon Webhook Implementation - a49000.win

## Webhook URL Configuration
**URL to configure in Drakon panel:**
```
https://a49000.win/drakon_api
```

**Alternative URLs (all point to same controller):**
- `https://a49000.win/webhook/drakon`
- `https://a49000.win/api/drakon_api`

---

## Implementation Details

### Framework & Version
- **Platform:** Laravel 10.50.0
- **PHP Version:** 8.2.12
- **Controller:** `app/Http/Controllers/Api/DrakonController.php`
- **Method:** `webhook(Request $request)`

### Routes Configuration
**File:** `routes/web.php`

```php
Route::match(['get', 'post'], '/drakon_api', [\App\Http\Controllers\Api\DrakonController::class, 'webhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::match(['get', 'post'], '/webhook/drakon', [\App\Http\Controllers\Api\DrakonController::class, 'webhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::match(['get', 'post'], '/api/drakon_api', [\App\Http\Controllers\Api\DrakonController::class, 'webhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

**CSRF Token:** Disabled for these routes
**HTTP Methods:** GET and POST supported

---

## Webhook Methods Implementation

### 1. account_details
**Request Parameters:**
- `method`: "account_details"
- `user_id`: User ID

**Response Format:**
```json
{
  "user_id": "1",
  "email": "user@example.com",
  "name_jogador": "Player Name"
}
```

**Error Responses:**
```json
{"status": false, "error": "INVALID_USER"}
```

---

### 2. user_balance
**Request Parameters:**
- `method`: "user_balance"
- `user_id`: User ID

**Response Format:**
```json
{
  "status": 1,
  "balance": 100.50
}
```

**Balance Calculation:**
- Returns sum of `balance` + `balance_bonus` from wallet table
- Formatted to 2 decimal places

---

### 3. transaction_bet
**Request Parameters:**
- `method`: "transaction_bet"
- `user_id`: User ID
- `transaction_id`: Unique transaction identifier
- `bet`: Bet amount (float)
- `round_id`: Game round ID
- `game`: Game identifier

**Response Format:**
```json
{
  "status": 1,
  "balance": 95.50
}
```

**Business Logic:**
1. Check for duplicate transaction_id
2. Verify user wallet exists and is active
3. Check sufficient balance (balance + balance_bonus)
4. Debit from `balance` first, then `balance_bonus` if needed
5. Create order record in database
6. Return updated balance

**Error Responses:**
```json
{"status": false, "error": "NO_BALANCE"}
{"status": false, "error": "INVALID_USER"}
{"status": false, "error": "INVALID_PARAMS"}
```

---

### 4. transaction_win
**Request Parameters:**
- `method`: "transaction_win"
- `user_id`: User ID
- `transaction_id`: Unique transaction identifier
- `win`: Win amount (float, can be 0)
- `round_id`: Game round ID
- `game`: Game identifier

**Response Format:**
```json
{
  "status": 1,
  "balance": 105.75
}
```

**Business Logic:**
1. Check for duplicate transaction_id
2. Verify user wallet exists
3. Credit win amount to `balance`
4. Create order record
5. Return updated balance

**Special Cases:**
- If win = 0, still create record but don't modify balance
- Negative win amounts return status: 0

---

### 5. refund
**Request Parameters:**
- `method`: "refund"
- `user_id`: User ID
- `transaction_id`: Original transaction ID to refund
- `amount`: Refund amount
- `round_id`: Game round ID
- `game`: Game identifier

**Response Format:**
```json
{
  "status": true,
  "balance": 100.00
}
```

**Business Logic:**
1. Find original transaction by transaction_id
2. Reverse the transaction:
   - If original was BET: credit amount back to user
   - If original was WIN: debit amount from user
3. Mark original order as `refunded`
4. Return updated balance

**Error Responses:**
```json
{"status": false, "error": "INVALID_TRANSACTION"}
{"status": false, "error": "INVALID_USER"}
```

---

### 6. cancel
**Request Parameters:**
- `method`: "cancel"
- `user_id`: User ID
- `transaction_id`: Transaction ID to cancel
- `round_id`: Game round ID
- `game`: Game identifier
- `amount`: Amount (optional)

**Response Format:**
```json
{
  "status": true,
  "transaction_status": "CANCELED"
}
```

**Business Logic:**
1. Find original transaction by transaction_id
2. Reverse the transaction:
   - If original was BET: credit amount back to user
   - If original was WIN: debit amount from user
3. Mark original order as `canceled`
4. Return status

---

## Database Tables

### wallets
- `user_id`: Foreign key to users table
- `balance`: Main balance (DECIMAL)
- `balance_bonus`: Bonus balance (DECIMAL)
- `total_balance`: Virtual column (balance + balance_bonus)
- `active`: Boolean flag

### orders
- `user_id`: Foreign key to users table
- `transaction_id`: Unique transaction identifier (indexed)
- `session_id`: Game session/round ID
- `game`: Game identifier
- `type`: 'bet' or 'win'
- `amount`: Transaction amount (DECIMAL)
- `providers`: 'drakon'
- `round_id`: Game round ID
- `status`: 'completed', 'refunded', 'canceled'
- `refunded`: Boolean flag

---

## Logging & Debugging

All webhook requests are logged to `storage/logs/laravel.log`:

```php
Log::info('========== DRAKON WEBHOOK RECEIVED ==========');
Log::info('Method: ' . $method);
Log::info('Data: ' . json_encode($data, JSON_PRETTY_PRINT));
Log::info('IP: ' . $request->ip());
```

**Log entries include:**
- Request method
- All parameters
- Request IP
- Headers
- Processing results

---

## Security Features

1. **CSRF Protection:** Disabled for webhook routes
2. **Duplicate Transaction Prevention:** Check transaction_id before processing
3. **Balance Validation:** Verify sufficient funds before bet
4. **Database Transactions:** Atomic operations for balance updates
5. **Error Handling:** Try-catch blocks with logging

---

## Testing Endpoints

### Simple connectivity test:
```
GET https://a49000.win/drakon_api
```
**Response:**
```json
{
  "status": true,
  "message": "Drakon webhook endpoint is active",
  "timestamp": "2025-12-09T12:00:00Z"
}
```

### Debug endpoint:
```
GET https://a49000.win/drakon-debug.php
```
Returns server configuration and registered routes.

---

## Server Configuration

- **Web Server:** Nginx with CloudPanel
- **SSL/TLS:** Full (strict) via Cloudflare
- **Document Root:** `/home/a49000/htdocs/a49000.win/public`
- **PHP-FPM:** Port 8080 backend

### Required Server Commands (after code changes):
```bash
cd /home/a49000/htdocs/a49000.win
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

---

## Common Issues & Solutions

### Issue: 404 Error
**Cause:** Routes cached or SSL configuration
**Solution:** 
1. Clear Laravel cache
2. Verify Cloudflare SSL/TLS is "Full (strict)"
3. Check Nginx configuration

### Issue: CSRF Token Mismatch
**Cause:** Middleware not excluded
**Solution:** Routes use `withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])`

### Issue: Redirect Loop
**Cause:** Cloudflare SSL mode is "Flexible"
**Solution:** Change to "Full" or "Full (strict)"

---

## Contact Information
- **Domain:** a49000.win
- **Environment:** Production
- **Hosting:** CloudPanel
- **CDN:** Cloudflare

---

## Response Format Standards

All responses follow Drakon API specifications:

✅ **Correct Response Formats:**
- Status codes: `1` (success) or `0` (failure) as integers
- Balance: Float with 2 decimal places
- Flat structure (no nested "data" objects)

❌ **Incorrect (previous implementation):**
- Status: `true`/`false` as booleans
- Nested data structures

---

## Code Location

**Full implementation:** `app/Http/Controllers/Api/DrakonController.php`

The webhook method handles request routing to appropriate handlers based on the `method` parameter.
