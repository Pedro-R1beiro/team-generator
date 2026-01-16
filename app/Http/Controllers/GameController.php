<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\TeamSet;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function store(Request $request, TeamSet $teamSet)
    {
        $data = $request->validate([
            'team_1' => 'required|in:A,B,C,D',
            'team_2' => 'required|in:A,B,C,D|different:team_1',
            'score_1' => 'nullable|integer|min:0',
            'score_2' => 'nullable|integer|min:0',
            'winner' => 'nullable|in:A,B,C,D',
        ]);

        return response()->json(
            $teamSet->games()->create($data)
        );
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return response()->json(['success' => true]);
    }
}
