<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutoTeamsController extends Controller
{
    public function autoPlayers(Request $request)
    {
        $rawText = $request->input('players', '');

        /**
         * 1️⃣ Quebra linhas + limpa (somente letras e espaços)
         */
        $inputNames = collect(
            preg_split("/\r\n|\n|\r/", $rawText)
        )
            ->map(fn ($name) => trim(preg_replace('/[^a-zA-ZÀ-ÿ\s]/u', '', $name))
            )
            ->filter()
            ->unique()
            ->values();

        /**
         * 2️⃣ Carrega usuários uma vez (id + name)
         */
        $users = \App\Models\User::query()
            ->get(['id', 'name']);

        /**
         * Função local de normalização
         */
        $normalize = function ($value) {
            $value = mb_strtolower($value);

            return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        };

        $found = collect();
        $notFound = collect();

        /**
         * 3️⃣ Match inteligente por tokens
         */
        foreach ($inputNames as $inputName) {
            $tokens = collect(
                explode(' ', $normalize($inputName))
            )->filter();

            $matchedUser = $users->first(function ($user) use ($tokens, $normalize) {
                $normalizedUserName = $normalize($user->name);

                return $tokens->every(
                    fn ($token) => str_contains($normalizedUserName, $token)
                );
            });

            if ($matchedUser) {
                $found->push([
                    'id' => $matchedUser->id,
                    'name' => $matchedUser->name,
                ]);
            } else {
                $notFound->push($inputName);
            }
        }

        /**
         * 4️⃣ Retorno
         */
        return response()->json([
            'found' => $found->values(),
            'not_found' => $notFound->values(),
        ]);
    }
}
