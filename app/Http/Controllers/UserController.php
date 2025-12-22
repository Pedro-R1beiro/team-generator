<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name')->get();

        return view('players', compact('users'));
    }

    public function store(Request $request)
    {
        $simpleStr = Str::of($request->name)->lower()->ascii()->replace(' ', '');
        $password = $simpleStr;

        if (strlen($simpleStr) < 8) {
            $missing = 8 - strlen($simpleStr);
            $password .= substr('12345678', 0, $missing);
        }

        User::create([
            'name' => $request->name,
            'email' => $simpleStr.'@system.com',
            'password' => Hash::make($password),
        ]);

        return redirect(route('players'));
    }
}
