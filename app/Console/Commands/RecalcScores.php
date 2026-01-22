<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\User;
use App\Services\ScoreService;
use Illuminate\Console\Command;

class RecalcScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalc-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1️⃣ Zera tudo
        User::query()->update([
            'games_played' => 0,
            'games_won' => 0,
            'score' => 0,
        ]);

        // 2️⃣ Atualiza totals via SQL
        Game::with('teamSet.players.user')
            ->chunk(100, function ($games) {

                foreach ($games as $game) {
                    foreach ($game->teamSet->players as $player) {

                        User::whereKey($player->user_id)
                            ->increment('games_played');

                        if ($player->team === $game->winner) {
                            User::whereKey($player->user_id)
                                ->increment('games_won');
                        }
                    }
                }

            });

        // 3️⃣ Calcula score usando valores REAIS do banco
        User::select('id', 'games_played', 'games_won')
            ->chunk(100, function ($users) {

                foreach ($users as $user) {
                    User::whereKey($user->id)->update([
                        'score' => ScoreService::calculate(
                            $user->games_played,
                            $user->games_won
                        ),
                    ]);
                }

            });

        $this->info('Scores recalculados com sucesso');
    }
}
