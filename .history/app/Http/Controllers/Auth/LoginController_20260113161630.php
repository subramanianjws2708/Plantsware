<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('view.login');
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback and login/create user.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google authentication failed. Please try again.');
        }
    
        $user = User::where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();
    
        if ($user) {
            $user->update([
                'google_id' => $googleUser->id,
                'name'      => $user->name ?? $googleUser->name,
                'avatar'    => $user->avatar ?? $googleUser->avatar,
            ]);
        } else {
            $user = User::create([
                'name'              => $googleUser->name,
                'email'             => $googleUser->email,
                'google_id'         => $googleUser->id,
                'avatar'            => $googleUser->avatar,
                'password'          => Hash::make(str()->random(32)),
                'email_verified_at' => now(),
            ]);
        }
    
        Auth::login($user, true);
    lO
        return redirect()->route('user.dashboard')
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }
    

    /**
     * The user has been authenticated.
     * This method overrides default redirect behavior.
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        return redirect()->intended('/dashboard');
    }
}