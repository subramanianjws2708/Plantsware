<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    // No constructor needed — middleware applied in routes

    public function showLoginForm()
    {
        return view('view.login'); // your custom login view path
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }

        // Step 1: Try to find by google_id (fastest for returning users)
        $user = User::where('google_id', $googleUser->id)->first();

        // Step 2: If not found by google_id → try by phone (if Google ever gives phone - rare!)
        //        Most important → then by email
        if (!$user) {
            // Very few countries give phone → mostly you'll rely on email
            $phone = $googleUser->user['phone_number'] ?? null; // almost always null

            $user = User::query()
                ->when($phone, function ($q) use ($phone) {
                    return $q->where('phone', $phone);
                })
                ->orWhere('email', $googleUser->email)
                ->first();
        }

        // Step 3: Create or update
        if ($user) {
            // Existing user → link google account / update data
            $user->update([
                'google_id'     => $googleUser->id,
                'name'          => $user->name ?? $googleUser->name,
                'avatar'        => $user->avatar ?? $googleUser->avatar,
                'email'         => $user->email ?? $googleUser->email, // protect existing email
                // Do NOT overwrite phone! It's sacred
            ]);
        } else {
            // Brand new user
            $user = User::create([
                'name'          => $googleUser->name,
                'email'         => $googleUser->email,
                'google_id'     => $googleUser->id,
                'avatar'        => $googleUser->avatar,
                'password'      => Hash::make(uniqid() . random_bytes(16)), // secure random
                'email_verified_at' => now(), // Google already verified it
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended('/');
    }
}