<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login\ValidateRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{

    use \App\Http\Traits\Auth;

    public function validateLogin(ValidateRequest $request)
    {
        if ( !$this->doesUserExists($request) ) {
            return self::api(false, __('auth.login.username-exists'), [], 404);
        }

        return $this->js([
            'ok' => true,
            'expiry' => Carbon::now()->addMinutes(5)->toDateTimeString()
        ]);
    }

    public static function otpLogin($request)
    {
        $user = (new self)->getUserByName($request);

        if ( $user->doesntExist() ) {
            return self::api(false, __('auth.login.username-exists'), [], 404);
        }

        Auth::login( $user->first() );

        $request->session()->regenerate();

        return self::api(
            true,
            __('auth.login.success'),
            [
                'notify' => 'info',
                'next' => RouteServiceProvider::HOME,
                'user' => $request->user()->load('permissions')
            ]
        );
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordLogin(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'redirect' => true,
            'location' => RouteServiceProvider::HOME,
            'user' => $request->user()->load('permissions')
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logOut(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
