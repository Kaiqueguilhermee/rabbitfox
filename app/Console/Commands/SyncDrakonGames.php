<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\GamesKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncDrakonGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drakon:sync-games {--force : Force update existing games}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync games from Drakon API and download images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Drakon games sync...');

        // Get credentials
        $keys = GamesKey::first();
        $agentCode = $keys->drakon_agent_code ?? env('DRAKON_AGENT_CODE');
        $agentToken = $keys->drakon_agent_token ?? env('DRAKON_AGENT_TOKEN');

        if (empty($agentCode) || empty($agentToken)) {
            $this->error('Drakon credentials not configured in games_keys or .env');
            return 1;
        }

        // Fetch games from Drakon API
        $this->info('Fetching games from Drakon API...');
        
        try {
            $response = Http::get('https://api.drakon.casino/games', [
                'agent_code' => $agentCode,
                'agent_token' => $agentToken,
            ]);

            if (!$response->successful()) {
                $this->error('Failed to fetch games from Drakon API: ' . $response->status());
                return 1;
            }

            $games = $response->json();
            
            if (empty($games) || !is_array($games)) {
                $this->warn('No games returned from API');
                return 0;
            }

            $this->info('Found ' . count($games) . ' games from Drakon API');

            $progressBar = $this->output->createProgressBar(count($games));
            $progressBar->start();

            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($games as $gameData) {
                $uuid = $gameData['game_code'] ?? $gameData['uuid'] ?? null;
                $name = $gameData['game_name'] ?? $gameData['name'] ?? null;
                $imageUrl = $gameData['image_url'] ?? $gameData['image'] ?? null;
                $provider = $gameData['provider'] ?? 'PGSoft';

                if (empty($uuid) || empty($name)) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Check if game exists
                $game = Game::where('uuid', $uuid)->first();

                if ($game && !$this->option('force')) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Download image if URL provided
                $imagePath = null;
                if (!empty($imageUrl)) {
                    try {
                        $imageContent = Http::get($imageUrl)->body();
                        $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $filename = Str::slug($name) . '-' . time() . '.' . $extension;
                        
                        Storage::disk('public')->put($filename, $imageContent);
                        $imagePath = $filename;
                    } catch (\Exception $e) {
                        $this->warn("\nFailed to download image for {$name}: " . $e->getMessage());
                    }
                }

                // Create or update game
                $gameData = [
                    'name' => $name,
                    'uuid' => $uuid,
                    'image' => $imagePath ?? 'default-game.png',
                    'type' => 'slots',
                    'provider' => $provider,
                    'provider_service' => 'drakon',
                    'technology' => 'HTML5',
                    'slug' => Str::slug($name),
                    'active' => 1,
                ];

                if ($game) {
                    $game->update($gameData);
                    $updated++;
                } else {
                    Game::create($gameData);
                    $created++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);
            
            $this->info("Sync completed!");
            $this->info("Created: {$created}");
            $this->info("Updated: {$updated}");
            $this->info("Skipped: {$skipped}");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error syncing games: ' . $e->getMessage());
            return 1;
        }
    }
}
