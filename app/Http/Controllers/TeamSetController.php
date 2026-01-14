<?php

namespace App\Http\Controllers;

use App\Models\TeamSet;
use Illuminate\Http\Request;

class TeamSetController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'teams' => 'required|array',
        ]);

        $teamSet = TeamSet::create([
            'name' => $request->name,
            'played_at' => $request->played_at,
        ]);

        foreach ($request->teams as $team => $users) {
            foreach ($users as $userId) {
                $teamSet->players()->create([
                    'user_id' => $userId,
                    'team' => $team,
                ]);
            }
        }

        return response()->json([
            'id' => $teamSet->id,
            'name' => $teamSet->name,
            'played_at' => $teamSet->played_at,
            'message' => 'Times criados com sucesso!',
        ]);
    }

    public function index()
    {
        return TeamSet::orderByDesc('created_at')->get();
    }

    public function show(TeamSet $teamSet)
    {
        return $teamSet->load('players.user');
    }

    public function update(Request $request, TeamSet $teamSet)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'played_at' => 'nullable|date',
            'teams' => 'required|array',
            'teams.*' => 'array',
        ]);

        // Atualiza dados principais
        $teamSet->update([
            'name' => $validated['name'] ?? $teamSet->name,
            'played_at' => $validated['played_at'] ?? $teamSet->played_at,
        ]);

        // Remove jogadores antigos
        $teamSet->players()->delete();

        // Recria jogadores
        foreach ($validated['teams'] as $team => $users) {
            foreach ($users as $userId) {
                $teamSet->players()->create([
                    'user_id' => $userId,
                    'team' => $team,
                ]);
            }
        }

        return response()->json([
            'id' => $teamSet->id,
            'name' => $teamSet->name,
            'played_at' => $teamSet->played_at,
            'message' => 'Times atualizados com sucesso!',
        ]);
    }

    public function destroy(TeamSet $teamSet)
    {
        $teamSet->delete();

        return response()->json(['success' => true]);
    }
}
