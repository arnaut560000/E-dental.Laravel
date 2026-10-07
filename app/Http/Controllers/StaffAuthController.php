<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        $authenticated = Auth::attemptWhen(
            [
                'username' => strtolower(trim($data['username'])),
                'password' => $data['password'],
                'is_active' => true,
            ],
            fn (User $user) => ! $user->must_change_password
                && in_array($user->role, ['admin', 'scheduler'], true)
        );

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'username' => 'Unable to sign in with these credentials.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('staff.requests.index'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
