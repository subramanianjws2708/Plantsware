<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect to Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            // ❌ REMOVE stateless() when using database sessions
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password'  => Hash::make(uniqid()), // REQUIRED
                ]);
            }

            // ✅ Login user
            Auth::login($user);

            // ✅ VERY IMPORTANT for database sessions
            request()->session()->regenerate();

            return redirect()->route('user.dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google login failed');
        }
    }
}
