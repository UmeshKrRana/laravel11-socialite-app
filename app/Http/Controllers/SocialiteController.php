<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SocialiteController extends Controller
{
    /**
     * Function: authProviderRedirect
     * Description: This function will redirect to Given Provider
     * @param NA
     * @return void
     */
    public function authProviderRedirect($provider) {
        if ($provider) {
            return Socialite::driver($provider)->redirect();
        }
        abort(404);
    }

    /**
     * Function: googleAuthentication
     * Decription: This function will authenticate the user through the Google Account
     * @param NA
     * @return void
     */
    public function socialAuthentication($provider) {
        try {
            if ($provider) {
                $socialUser = Socialite::driver($provider)->user();

                $user = User::where('auth_provider_id', $socialUser->id)->first();

                if ($user) {
                    Auth::login($user);

                } else {
                    $userData = User::create([
                        'name' => $socialUser->name,
                        'email' => $socialUser->email,
                        'password' => Hash::make('Password@1234'),
                        'auth_provider_id' => $socialUser->id,
                        'auth_provider' => $provider,
                    ]);

                    if ($userData) {
                        Auth::login($userData);
                    }
                }

                return redirect()->route('dashboard');
            }
            abort(404);

        } catch (Exception $e) {
            dd($e);
        }
    }
}
