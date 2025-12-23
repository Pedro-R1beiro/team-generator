<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'is_admin')->get();

        return view('players', compact('users'));
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

        $password = $simpleStr;

        if (strlen($password) < 8) {
            $password .= substr('12345678', 0, 8 - strlen($password));
        }

        User::create([
            'name' => $validated['name'],
            'email' => $simpleStr.'@system.com',
            'password' => Hash::make($password),
        ]);

        return redirect()
            ->route('players')
            ->with('success', 'Player added successfully!');
    }

    public function update(Request $request, User $user)
    {
        dd($request->all());

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $user->update([
            'name' => $validated['name'],
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