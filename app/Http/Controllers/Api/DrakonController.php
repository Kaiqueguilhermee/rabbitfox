<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\GamesKey;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DrakonController extends Controller
{
    /**
     * Credita valor no saldo principal do wallet (padrão para Drakon)
     */
    private function payWithRolloverDrakon($wallet, $amount)
    {
        // Aqui você pode adicionar lógica de rollover se necessário
        $wallet->increment('balance', $amount);
        $wallet->refresh();
        Log::info('payWithRolloverDrakon: valor creditado', ['user_id' => $wallet->user_id, 'valor' => $amount]);
    }
    /**
     * Authenticate agent and return access_token
     */
    public function authenticate(Request $request)
    {
        $data = $request->only(['token', 'secret']);

        if (empty($data['token']) || empty($data['secret'])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Compare against env configured agent credentials or values stored in DB
        $expectedToken = env('DRAKON_AGENT_TOKEN');
        $expectedSecret = env('DRAKON_AGENT_SECRET');

        if (empty($expectedToken) || empty($expectedSecret)) {
            $keys = GamesKey::first();
            if ($keys) {
                $expectedToken = $keys->drakon_agent_token ?? $expectedToken;
                $expectedSecret = $keys->drakon_agent_secret ?? $expectedSecret;
            }
        }

        if ($data['token'] === $expectedToken && $data['secret'] === $expectedSecret) {
            $access = base64_encode($data['token'] . ':' . $data['secret'] . ':' . time());
            return response()->json(['access_token' => $access], 201);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Launch a game and return game_url
     */
    public function gameLaunch(Request $request)
    {
        $params = $request->all();

        $agentCode = $request->query('agent_code');
        $agentToken = $request->query('agent_token');
        $gameId = $request->query('game_id');

        if (empty($agentCode) || empty($agentToken) || empty($gameId)) {
            return response()->json(['message' => 'Missing parameters'], 400);
        }

        $expectedAgentCode = env('DRAKON_AGENT_CODE');
        $expectedAgentToken = env('DRAKON_AGENT_TOKEN');

        if (empty($expectedAgentCode) || empty($expectedAgentToken)) {
            $keys = GamesKey::first();
            if ($keys) {
                $expectedAgentCode = $keys->drakon_agent_code ?? $expectedAgentCode;
                $expectedAgentToken = $keys->drakon_agent_token ?? $expectedAgentToken;
            }
        }

        if ($agentCode !== $expectedAgentCode || $agentToken !== $expectedAgentToken) {
            return response()->json(['message' => 'Invalid agent credentials'], 401);
        }

        $userId = $request->query('user_id');
        $userName = $request->query('user_name');
        $device = $request->query('device', 'desktop');
        $mode = $request->query('mode', 'real');

        $token = base64_encode($agentCode . ':' . $userId . ':' . time());

        $gameUrl = sprintf('https://gator.drakon.casino/play/%s?user_id=%s&user_name=%s&device=%s&mode=%s&token=%s',
            urlencode($gameId), urlencode($userId), urlencode($userName), $device, $mode, $token
        );

        return response()->json(['game_url' => $gameUrl], 201);
    }

    /**
     * Return all games (stubbed)
     */
    public function allGames(\Illuminate\Http\Request $request)
    {
        // Allow including inactive games for testing by passing ?include_inactive=1
        $includeInactive = $request->query('include_inactive') === '1';

        $query = $includeInactive ? Game::query() : Game::where('active', 1);

        $games = $query->get(['uuid', 'name', 'provider'])
            ->map(function ($g) {
                return [
                    'game_code' => $g->uuid,
                    'game_name' => $g->name,
                    'provider_game' => $g->provider,
                    'rtp' => null,
                ];
            })
            ->values();

        return response()->json($games, 200);
    }

    /**
     * Test page to load games and log to browser console
     */
    public function testGames()
    {
        // Provide agent credentials and current user info to the view so the browser can call game_launch
        $keys = GamesKey::first();
        $agentCode = $keys->drakon_agent_code ?? env('DRAKON_AGENT_CODE');
        $agentToken = $keys->drakon_agent_token ?? env('DRAKON_AGENT_TOKEN');
        $user = auth()->user();

        return view('drakon_test_games', [
            'agent_code' => $agentCode,
            'agent_token' => $agentToken,
            'user' => $user,
        ]);
    }

    /**
     * Launch game from server side to avoid exposing credentials in client
     * Requires authenticated user (use web route with auth middleware)
     */
    public function launchGameServer(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'game_id' => 'required|string',
        ]);

        $gameId = $request->input('game_id');

        // Get agent credentials from env or DB
        $keys = GamesKey::first();
        $agentCode = $keys->drakon_agent_code ?? env('DRAKON_AGENT_CODE');
        $agentToken = $keys->drakon_agent_token ?? env('DRAKON_AGENT_TOKEN');
        $agentSecret = $keys->drakon_agent_secret ?? env('DRAKON_AGENT_SECRET');

        if (empty($agentCode) || empty($agentToken) || empty($agentSecret)) {
            return response()->json(['message' => 'Agent credentials not configured'], 500);
        }

        $user = auth()->user();
        $userId = optional($user)->id ?? '';
        $userName = optional($user)->name ?? '';
        
        // Detect device from user agent
        $userAgent = $request->header('User-Agent', '');
        $isMobile = preg_match('/(android|iphone|ipad|mobile)/i', $userAgent);
        $device = $isMobile ? 'mobile' : 'desktop';

        // Call Drakon API to launch game
        try {
            // Step 1: Authenticate
            $credentials = base64_encode($agentToken . ':' . $agentSecret);
            
            $authResponse = \Illuminate\Support\Facades\Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $credentials,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('https://gator.drakon.casino/api/v1/auth/authentication', [
                    'token' => $agentToken,
                    'secret' => $agentSecret,
                ]);

            if (!$authResponse->successful()) {
                Log::error('Drakon auth failed', [
                    'status' => $authResponse->status(),
                    'body' => $authResponse->body(),
                ]);
                return response()->json(['message' => 'Authentication failed'], 401);
            }

            $authData = $authResponse->json();
            $accessToken = $authData['access_token'] ?? null;

            if (empty($accessToken)) {
                return response()->json(['message' => 'No access token received'], 500);
            }

            // Step 2: Launch game
            $queryParams = http_build_query([
                'agent_code' => $agentCode,
                'agent_token' => $agentToken,
                'game_id' => $gameId,
                'type' => 'CHARGED',
                'currency' => 'BRL',
                'lang' => 'pt_BR',
                'user_id' => $userId,
                'user_name' => $userName,
                'device' => $device,
                'mode' => 'real',
            ]);

            $apiUrl = 'https://gator.drakon.casino/api/v1/games/game_launch?' . $queryParams;
            
            $response = \Illuminate\Support\Facades\Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ])
                ->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['game_url'])) {
                    return response()->json(['game_url' => $data['game_url']], 201);
                }
            }

            Log::error('Drakon API game_launch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json(['message' => 'Failed to launch game', 'status' => $response->status()], 500);

        } catch (\Throwable $e) {
            Log::error('Drakon API game_launch exception: ' . $e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Return providers list (stubbed)
     */
    public function providers()
    {
        $providers = [
            ['code' => 'sample', 'name' => 'SampleProvider', 'rtp' => 95, 'status' => 1]
        ];

        return response()->json($providers, 200);
    }

    /**
     * Webhook receiver for Drakon API
     * Processa todos os métodos: account_details, user_balance, transaction_bet, 
     * transaction_win, refund, cancel
     */
    public function webhook(Request $request)
    {
        $data = $request->all();
        $rawBody = $request->getContent();
        
        Log::info('========== DRAKON WEBHOOK RECEIVED ==========');
        Log::info('Method: ' . ($request->input('method') ?? 'NONE'));
        Log::info('Data: ' . json_encode($data, JSON_PRETTY_PRINT));
        Log::info('Raw Body: ' . $rawBody);
        Log::info('Content-Type: ' . $request->header('Content-Type'));
        Log::info('IP: ' . $request->ip());
        Log::info('Full URL: ' . $request->fullUrl());
        Log::info('Request Method: ' . $request->method());
        Log::info('All Headers: ' . json_encode($request->headers->all()));
        Log::info('=============================================');

        $method = $request->input('method');

        // Se não tem method, retorna sucesso básico para teste de conectividade
        if (empty($method)) {
            return response()->json([
                'status' => true, 
                'message' => 'Drakon webhook endpoint is active',
                'timestamp' => now()->toIso8601String()
            ], 200);
        }

        try {
            switch ($method) {
                case 'account_details':
                    return $this->handleAccountDetails($request);

                case 'user_balance':
                    return $this->handleUserBalance($request);

                case 'transaction_bet':
                    return $this->handleTransactionBet($request);

                case 'transaction_win':
                    return $this->handleTransactionWin($request);

                case 'refund':
                    return $this->handleRefund($request);

                case 'cancel':
                    return $this->handleCancel($request);

                default:
                    Log::warning('Drakon webhook: Invalid method', ['method' => $method]);
                    return response()->json(['status' => false, 'error' => 'INVALID_METHOD'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Drakon webhook error: ' . $e->getMessage(), [
                'method' => $method,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => false, 'error' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * Handle account_details webhook
     */
    private function handleAccountDetails(Request $request)
    {
        $userId = $request->input('user_id');
        
        if (empty($userId)) {
            return response()->json(['status' => false, 'error' => 'INVALID_PARAMS'], 400);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['status' => false, 'error' => 'INVALID_USER'], 200);
        }

        return response()->json([
            'user_id' => (string) $user->id,
            'email' => $user->email,
            'name_jogador' => $user->name
        ], 200);
    }

    /**
     * Handle user_balance webhook
     */
    private function handleUserBalance(Request $request)
    {
        \Log::info('Drakon user_balance handler start', ['request' => $request->all()]);
        try {
            $userId = $request->input('user_id');
            if (empty($userId)) {
                \Log::warning('Drakon user_balance: user_id ausente', ['request' => $request->all()]);
                return response()->json(['status' => 0, 'error' => 'INVALID_PARAMS'], 400);
            }

            $wallet = Wallet::where('user_id', $userId)->first();
            if (!$wallet) {
                \Log::warning('Drakon user_balance: carteira não encontrada', ['user_id' => $userId]);
                return response()->json(['status' => 0, 'balance' => '0.00'], 200);
            }

            \Log::info('Drakon user_balance handler success', ['user_id' => $userId, 'balance' => $wallet->total_balance]);
            return response()->json([
                'status' => 1,
                'balance' => number_format($wallet->total_balance, 2, '.', '')
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Drakon user_balance exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['status' => false, 'error' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * Handle transaction_bet webhook
     */
    private function handleTransactionBet(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $transactionId = $request->input('transaction_id');
            $bet = (float) $request->input('bet');
            $roundId = $request->input('round_id');
            $game = $request->input('game');

            Log::info('Drakon BET request', [
                'user_id' => $userId,
                'transaction_id' => $transactionId,
                'bet' => $bet,
                'round_id' => $roundId,
                'game' => $game
            ]);

            if (!$userId || !$transactionId || $bet <= 0) {
                Log::warning('Drakon BET: Invalid params');
                return response()->json(['status' => 0, 'balance' => '0.00'], 200);
            }

        // Check for duplicate transaction
        $existingOrder = Order::where('transaction_id', $transactionId)->first();
        if ($existingOrder) {
            $wallet = Wallet::where('user_id', $userId)->first();
            return response()->json([
                'status' => 1,
                'balance' => number_format($wallet->total_balance ?? 0, 2, '.', '')
            ], 200);
        }

        $wallet = Wallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return response()->json(['status' => 0, 'balance' => '0.00'], 200);
        }

        if ($wallet->total_balance < $bet) {
            return response()->json(['status' => 0, 'balance' => number_format($wallet->total_balance, 2, '.', '')], 200);
        }


        // Debit from balance first, then balance_bonus
        if ($wallet->balance >= $bet) {
            $wallet->decrement('balance', $bet);
        } else {
            $remaining = $bet - $wallet->balance;
            $wallet->balance = 0;
            $wallet->decrement('balance_bonus', min($remaining, $wallet->balance_bonus));
        }

        // Deduct from rollover (balance_bonus_rollover) as well
        if ($wallet->balance_bonus_rollover > 0) {
            if ($wallet->balance_bonus_rollover >= $bet) {
                $wallet->decrement('balance_bonus_rollover', $bet);
            } else {
                $wallet->update(['balance_bonus_rollover' => 0]);
            }
        }

        $wallet->refresh();

        // Create order record
        Order::create([
            'user_id' => $userId,
            'session_id' => $roundId ?: Str::uuid(),
            'transaction_id' => $transactionId,
            'game' => $game,
            'game_uuid' => $game,
            'type' => 'bet',
            'type_money' => 'balance',
            'amount' => $bet,
            'providers' => 'drakon',
            'round_id' => $roundId,
            'status' => 1
        ]);

        Log::info('Drakon BET processed', [
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'amount' => $bet,
            'new_balance' => $wallet->total_balance
        ]);

        return response()->json([
            'status' => 1,
            'balance' => number_format($wallet->total_balance, 2, '.', '')
        ], 200);
        
        } catch (\Exception $e) {
            Log::error('Drakon BET exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId ?? null,
                'transaction_id' => $transactionId ?? null,
                'bet' => $bet ?? null
            ]);
            return response()->json(['status' => 0, 'balance' => '0.00'], 200);
        }
    }

    /**
     * Handle transaction_win webhook
     */
    private function handleTransactionWin(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $transactionId = $request->input('transaction_id');
            $win = (float) $request->input('win');
            $roundId = $request->input('round_id');
            $game = $request->input('game');

            if (!$userId || !$transactionId) {
                return response()->json(['status' => 0, 'balance' => '0.00'], 200);
            }

            // Check for duplicate transaction
            $existingOrder = Order::where('transaction_id', $transactionId)->first();
            if ($existingOrder) {
                $wallet = Wallet::where('user_id', $userId)->first();
                return response()->json([
                    'status' => 1,
                    'balance' => number_format($wallet->total_balance ?? 0, 2, '.', '')
                ], 200);
            }

            $wallet = Wallet::where('user_id', $userId)->first();
            if (!$wallet) {
                return response()->json(['status' => 0, 'balance' => '0.00'], 200);
            }

            if ($win < 0) {
                return response()->json(['status' => 0, 'balance' => '0.00'], 200);
            }

            // Credit to balance ou bônus com rollover
            if ($win > 0) {
                $this->payWithRolloverDrakon($wallet, $win);
            }

            // Create order record
            Order::create([
                'user_id' => $userId,
                'session_id' => $roundId ?: Str::uuid(),
                'transaction_id' => $transactionId,
                'game' => $game,
                'game_uuid' => $game,
                'type' => 'win',
                'type_money' => 'balance',
                'amount' => $win,
                'providers' => 'drakon',
                'round_id' => $roundId,
                'status' => 1
            ]);

            Log::info('Drakon WIN processed', [
                'user_id' => $userId,
                'transaction_id' => $transactionId,
                'amount' => $win,
                'new_balance' => $wallet->total_balance
            ]);

            return response()->json([
                'status' => 1,
                'balance' => number_format($wallet->balance, 2, '.', '')
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Drakon WIN exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['status' => false, 'error' => 'INTERNAL_ERROR'], 500);
        }
    }

    /**
     * Handle refund webhook
     */
    private function handleRefund(Request $request)
    {
        $userId = $request->input('user_id');
        $transactionId = $request->input('transaction_id');
        $amount = (float) $request->input('amount');
        $roundId = $request->input('round_id');
        $game = $request->input('game');

        if (!$userId || !$transactionId) {
            return response()->json(['status' => 0, 'balance' => '0.00'], 200);
        }

        // Find original transaction
        $originalOrder = Order::where('transaction_id', $transactionId)
            ->whereIn('type', ['bet', 'win'])
            ->first();

        if (!$originalOrder) {
            $wallet = Wallet::where('user_id', $userId)->first();
            return response()->json(['status' => 0, 'balance' => number_format($wallet->total_balance ?? 0, 2, '.', '')], 200);
        }

        $wallet = Wallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return response()->json(['status' => 0, 'balance' => '0.00'], 200);
        }

        // Reverse the transaction
        if ($originalOrder->type === 'bet') {
            // Refund bet - credit back to user
            $wallet->increment('balance', $originalOrder->amount);
        } elseif ($originalOrder->type === 'win') {
            // Reverse win - debit from user
            $wallet->decrement('balance', $originalOrder->amount);
        }

        $wallet->refresh();

        // Mark as refunded
        $originalOrder->update([
            'status' => 'refunded',
            'refunded' => 1
        ]);

        Log::info('Drakon REFUND processed', [
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'original_type' => $originalOrder->type,
            'amount' => $originalOrder->amount,
            'new_balance' => $wallet->total_balance
        ]);

        return response()->json([
            'status' => 1,
            'balance' => number_format($wallet->total_balance, 2, '.', '')
        ], 200);
    }

    /**
     * Handle cancel webhook
     */
    private function handleCancel(Request $request)
    {
        $userId = $request->input('user_id');
        $transactionId = $request->input('transaction_id');
        $roundId = $request->input('round_id');
        $game = $request->input('game');
        $amount = (float) $request->input('amount', 0);

        if (!$userId || !$transactionId) {
            return response()->json(['status' => 0, 'balance' => '0.00'], 200);
        }

        // Find original transaction
        $originalOrder = Order::where('transaction_id', $transactionId)
            ->whereIn('type', ['bet', 'win'])
            ->first();

        if (!$originalOrder) {
            $wallet = Wallet::where('user_id', $userId)->first();
            return response()->json(['status' => 0, 'balance' => number_format($wallet->total_balance ?? 0, 2, '.', '')], 200);
        }

        $wallet = Wallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return response()->json(['status' => 0, 'balance' => '0.00'], 200);
        }

        // Reverse the transaction
        if ($originalOrder->type === 'bet') {
            // Cancel bet - credit back to user
            $wallet->increment('balance', $originalOrder->amount);
        } elseif ($originalOrder->type === 'win') {
            // Cancel win - debit from user
            $wallet->decrement('balance', $originalOrder->amount);
        }

        $wallet->refresh();

        // Mark as canceled
        $originalOrder->update([
            'status' => 'canceled',
            'refunded' => 1
        ]);

        Log::info('Drakon CANCEL processed', [
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'original_type' => $originalOrder->type,
            'amount' => $originalOrder->amount,
            'new_balance' => $wallet->total_balance
        ]);

        return response()->json([
            'status' => 1,
            'balance' => number_format($wallet->total_balance, 2, '.', '')
        ], 200);
    }
}
