<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FiversGame;
use App\Models\FiversProvider;
use App\Models\Game;
use App\Models\GameExclusive;
use App\Models\VibraCasinoGame;
use Illuminate\Http\Request;
use App\Models\GamesKey;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Provider\SlotegratorController;
use App\Http\Controllers\Provider\VibraController;
use App\Http\Controllers\Provider\FiversController;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Only show Drakon games on the home page as requested
        $games = Game::when($searchTerm, function ($query) use ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%$searchTerm%")
                  ->orWhere('uuid', 'like', "%$searchTerm%");
            });
        })
                ->where(function($q) {
                        $q->where('provider_service', 'drakon')
                            ->orWhere('provider', 'drakon');
                })
                ->get();

        // TopTrend Gaming - Jogos Drakon em destaque
        $topTrendGames = Game::where(function($q) {
                    $q->where('provider_service', 'drakon')
                        ->orWhere('provider', 'drakon');
                })
                ->where('active', 1)
                ->orderBy('views', 'desc')
                ->limit(12)
                ->get();

        // Hide other sections (Fivers, Vibra, exclusives) to show only Drakon games
        $providers = collect();
        $gamesExclusives = collect();
        $gamesVibra = collect();

        $gamesExclusives = GameExclusive::when($searchTerm, function ($query) use ($searchTerm) {
            $query->where('name', 'like', "%$searchTerm%")
                ->orWhere('description', 'like', "%$searchTerm%");
        })
        ->whereActive(1)
        ->orderBy('views', 'desc')
        ->get();

        if ($gamesExclusives->isEmpty()) {
            $gamesExclusives = GameExclusive::when($searchTerm, function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%$searchTerm%")
                    ->orWhere('description', 'like', "%$searchTerm%");
            })->orderBy('views', 'desc')->get();
        }

        $providers = FiversProvider::with('games')
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%$searchTerm%");
            })
            ->whereHas('games', function ($query) use ($searchTerm) {
                $query->where('status', 1)
                    ->where('name', 'like', "%$searchTerm%")
                    ->orderBy('views', 'asc');
            })
            ->orderBy('name', 'desc')
            ->get();

        if ($providers->isEmpty()) {
            $providers = FiversProvider::with('games')
                ->when($searchTerm, function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%");
                })->orderBy('name', 'desc')->get();
        }

        $gamesVibra = VibraCasinoGame::when($searchTerm, function ($query) use ($searchTerm) {
            $query->where('game_name', 'like', "%$searchTerm%");
        })
        ->whereStatus(1)
        ->limit(24)
        ->get();

        if ($gamesVibra->isEmpty()) {
            $gamesVibra = VibraCasinoGame::when($searchTerm, function ($query) use ($searchTerm) {
                $query->where('game_name', 'like', "%$searchTerm%");
            })->limit(24)->get();
        }

        // Debug endpoint: visit /?debug_games=1 to get JSON with counts and samples
        if ($request->query('debug_games') == '1') {
            try {
                return response()->json([
                    'games_count' => $games->count(),
                    'games_sample' => $games->map(fn($g) => ['id' => $g->id, 'name' => $g->name, 'uuid' => $g->uuid, 'active' => $g->active, 'provider' => $g->provider, 'provider_service' => $g->provider_service])->take(50),
                    'gamesExclusives_count' => $gamesExclusives->count(),
                    'providers_count' => $providers->count(),
                    'gamesVibra_count' => $gamesVibra->count(),
                ]);
            } catch (\Throwable $e) {
                Log::error('HomeController debug_games failed: ' . $e->getMessage());
                return response()->json(['error' => 'debug_failed', 'message' => $e->getMessage()], 500);
            }
        }

        $categoriesHome = Category::where('show_on_home', 1)->with(['gamesSlotgrator' => function($q) {
            $q->where('active', 1);
        }])->get();

        return view('web.home.index', [
            'games' => $games,
            'topTrendGames' => $topTrendGames,
            'providers' => $providers,
            'gamesExclusives' => $gamesExclusives,
            'gamesVibra' => $gamesVibra,
            'categoriesHome' => $categoriesHome,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Play a game: if Drakon provider, generate server-side launch and redirect.
     */
    public function playGame(Request $request, string $uuid)
    {
        $game = Game::where('uuid', $uuid)->first();
        if (!$game) return back();
        // Determine service: prefer provider_service, fallback to provider
        $service = strtolower($game->provider_service ?? $game->provider ?? '');

        // Debug mode: return diagnostic info when requested (authenticated)
        if ($request->query('debug_play') == '1') {
            $keys = GamesKey::first();
            return response()->json([
                'game_id' => $game->id,
                'name' => $game->name,
                'provider' => $game->provider,
                'provider_service' => $game->provider_service,
                'service_detected' => $service,
                'drakon_agent_code' => $keys->drakon_agent_code ?? null,
                'drakon_agent_token' => $keys->drakon_agent_token ?? null,
            ]);
        }

        // If game provider/service is Drakon (case-insensitive), generate server-side game URL
        if ($service === 'drakon' || strtolower($game->provider) === 'drakon') {
            $keys = GamesKey::first();
            $agentCode = $keys->drakon_agent_code ?? env('DRAKON_AGENT_CODE');
            $agentToken = $keys->drakon_agent_token ?? env('DRAKON_AGENT_TOKEN');
            $agentSecret = $keys->drakon_agent_secret ?? env('DRAKON_AGENT_SECRET');

            if (empty($agentCode) || empty($agentToken) || empty($agentSecret)) {
                return back()->with('error', 'Drakon credentials not configured');
            }

            $user = auth()->user();
            $userId = optional($user)->id ?? '';
            $userName = optional($user)->name ?? '';
            
            // Detect device from user agent
            $userAgent = $request->header('User-Agent', '');
            $isMobile = preg_match('/(android|iphone|ipad|mobile)/i', $userAgent);
            $device = $isMobile ? 'mobile' : 'desktop';

            // Call Drakon API to launch game
            $mode = 'real';
            try {
                // Step 1: Authenticate to get access_token
                $credentials = base64_encode($agentToken . ':' . $agentSecret);
                
                Log::info('Drakon authentication', [
                    'endpoint' => 'https://gator.drakon.casino/api/v1/auth/authentication',
                ]);
                
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

                Log::info('Drakon auth response', [
                    'status' => $authResponse->status(),
                    'body' => $authResponse->body(),
                ]);

                if (!$authResponse->successful()) {
                    Log::error('Drakon authentication failed', [
                        'status' => $authResponse->status(),
                        'body' => $authResponse->body(),
                    ]);
                    return back()->with('error', 'Erro na autenticação. Status: ' . $authResponse->status());
                }

                $authData = $authResponse->json();
                $accessToken = $authData['access_token'] ?? null;

                if (empty($accessToken)) {
                    Log::error('Drakon access_token not returned', ['response' => $authData]);
                    return back()->with('error', 'Token de acesso não recebido.');
                }

                Log::info('Drakon auth successful', ['has_token' => true]);

                // Step 2: Launch game with access token
                // Send parameters in request body instead of query string to avoid URL length issues
                $apiUrl = 'https://gator.drakon.casino/api/v1/games/game_launch';
                
                // Build full URL with query parameters
                $fullUrl = $apiUrl . '?' . http_build_query([
                    'agent_code' => $agentCode,
                    'agent_token' => $agentToken,
                    'game_id' => $uuid,
                    'type' => 'CHARGED',
                    'currency' => 'BRL',
                    'lang' => 'pt_BR',
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'device' => $device,
                    'mode' => $mode,
                ]);

                Log::info('Drakon game launch request', [
                    'endpoint' => $fullUrl,
                    'game_id' => $uuid,
                ]);
                
                // Try using cURL directly to avoid chunked encoding issues
                $ch = curl_init($fullUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $accessToken,
                        'Accept: application/json',
                    ],
                    CURLOPT_ENCODING => '', // Accept all encodings
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                ]);
                
                $rawBody = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                // Create response-like object for compatibility
                $response = new class($rawBody, $statusCode) {
                    private $body;
                    private $status;
                    
                    public function __construct($body, $status) {
                        $this->body = $body;
                        $this->status = $status;
                    }
                    
                    public function body() { return $this->body; }
                    public function status() { return $this->status; }
                    public function successful() { return $this->status >= 200 && $this->status < 300; }
                    public function json() { return json_decode($this->body, true); }
                    public function headers() { return []; }
                };

                Log::info('========== DRAKON API RESPONSE (cURL) ==========');
                Log::info('Status: ' . $statusCode);
                Log::info('Body Length: ' . strlen($rawBody));
                Log::info('Body: ' . $rawBody);
                if (!empty($error)) {
                    Log::info('cURL Error: ' . $error);
                }
                Log::info('=========================================');

                if ($response->successful()) {
                    $data = $response->json();
                    
                    Log::info('Drakon parsed JSON', [
                        'game_url_from_json' => $data['game_url'] ?? 'NOT FOUND',
                        'game_url_length' => isset($data['game_url']) ? strlen($data['game_url']) : 0,
                        'all_keys' => array_keys($data),
                    ]);
                    
                    if (!empty($data['game_url'])) {
                        $gameUrl = $data['game_url'];
                        
                        // Try to extract full URL from raw body if truncated
                        if (strlen($gameUrl) < 200) {
                            preg_match('/"game_url":"([^"]+)"/', $rawBody, $matches);
                            if (!empty($matches[1])) {
                                $extractedUrl = str_replace('\/', '/', $matches[1]);
                                Log::info('Drakon extracted URL from raw', [
                                    'extracted_url' => $extractedUrl,
                                    'extracted_length' => strlen($extractedUrl),
                                ]);
                                $gameUrl = $extractedUrl;
                            }
                        }
                        
                        Log::info('Drakon final redirect URL', [
                            'game_url' => $gameUrl,
                            'url_length' => strlen($gameUrl),
                        ]);
                        
                        // Increment game views
                        $game->increment('views', 1);
                        
                        // Return view with iframe instead of redirecting
                        return view('web.game.index', [
                            'game' => $game,
                            'gameUrl' => $gameUrl,
                        ]);
                    }
                }

                Log::error('Drakon game launch failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return back()->with('error', 'Erro ao iniciar o jogo. Status: ' . $response->status());

            } catch (\Throwable $e) {
                Log::error('Drakon game launch exception: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->with('error', 'Erro ao conectar: ' . $e->getMessage());
            }
        }

        // Slotegrator provider: use SlotegratorTrait flow
        if ($service === 'slotegrator') {
            try {
                $sloteController = app()->make(SlotegratorController::class);
                $result = $sloteController->startGameSlotegrator($game->uuid);
                if (is_array($result) && ($result['status'] ?? false) && !empty($result['game_url'])) {
                    return redirect()->away($result['game_url']);
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Erro iniciando jogo Slotegrator: ' . $e->getMessage());
            }
        }

        // Vibra provider: try to use VibraController if available
        if ($service === 'vibra') {
            try {
                $vibraController = app()->make(VibraController::class);
                // If there's a matching VibraCasinoGame record, use it; otherwise call index
                return $vibraController->index();
            } catch (\Throwable $e) {
                return back()->with('error', 'Erro iniciando jogo Vibra: ' . $e->getMessage());
            }
        }

        // Fivers provider: delegate to FiversController if applicable
        if ($service === 'fivers' || strtolower($game->provider) === 'fivers') {
            try {
                $fiversController = app()->make(FiversController::class);
                // FiversController expects FiversGame records; redirect to generic games page for now
                return redirect()->route('web.game.index', ['slug' => $game->slug]);
            } catch (\Throwable $e) {
                return back()->with('error', 'Erro iniciando jogo Fivers: ' . $e->getMessage());
            }
        }

        // Fallback: redirect to existing game page
        return redirect()->route('web.game.index', ['slug' => $game->slug]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function banned()
    {
        return view('web.banned.index');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function howWorks()
    {
        return view('web.home.how-works');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function aboutUs()
    {
        return view('web.home.about-us');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function suporte()
    {
        return view('web.home.suporte');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function showGameByCategory(string $slug)
    {
        $category = Category::where('slug', $slug)->first();

        if(!empty($category)) {
            $games = Game::where('category_id', $category->id)->whereActive(1)->paginate(18);
            $gamesFivers = collect(); // Vazio, não exibir jogos Fivers

            return view('web.categories.index', compact(['games', 'gamesFivers', 'category']));
        }

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
