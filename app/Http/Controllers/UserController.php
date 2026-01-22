<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'is_admin', 'score')
            ->orderByDesc('is_admin')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $users = $query->paginate(18)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        $view = match (Route::currentRouteName()) {
            'teams' => 'admin.teams',
            default => 'admin.players',
        };

        return view($view, compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $simpleStr = Str::of($validated['name'])
            ->lower()
            ->ascii()
            ->replace(' ', '');

        $email = $simpleStr.'@system.com';

        $password = $simpleStr;
        if (strlen($password) < 8) {
            $password .= substr('12345678', 0, 8 - strlen($password));
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Player added successfully!',
                'user' => $user,
            ]);
        }

        return redirect()
            ->route('players')
            ->with('success', 'Player added successfully!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $simpleStr = Str::of($validated['name'])
            ->lower()
            ->ascii()
            ->replace(' ', '');

        $email = $simpleStr.'@system.com';

        $user->update([
            'name' => $validated['name'],
            'email' => $email,
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()
            ->route('players')
            ->with('success', 'Player updated successfully!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('players')
            ->with('success', 'Player deleted successfully!');
    }
}
