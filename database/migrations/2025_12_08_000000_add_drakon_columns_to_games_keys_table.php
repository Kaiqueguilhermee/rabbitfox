<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('games_keys', function (Blueprint $table) {
            if (!Schema::hasColumn('games_keys', 'drakon_agent_code')) {
                $table->string('drakon_agent_code')->nullable()->after('vibra_game_mode');
            }
            if (!Schema::hasColumn('games_keys', 'drakon_agent_token')) {
                $table->string('drakon_agent_token')->nullable()->after('drakon_agent_code');
            }
            if (!Schema::hasColumn('games_keys', 'drakon_agent_secret')) {
                $table->string('drakon_agent_secret')->nullable()->after('drakon_agent_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games_keys', function (Blueprint $table) {
            if (Schema::hasColumn('games_keys', 'drakon_agent_secret')) {
                $table->dropColumn('drakon_agent_secret');
            }
            if (Schema::hasColumn('games_keys', 'drakon_agent_token')) {
                $table->dropColumn('drakon_agent_token');
            }
            if (Schema::hasColumn('games_keys', 'drakon_agent_code')) {
                $table->dropColumn('drakon_agent_code');
            }
        });
    }
};
