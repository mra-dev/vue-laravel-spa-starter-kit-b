<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Verify\SendRequest;
use App\Http\Requests\Auth\Verify\VerifyRequest;
use App\Http\Traits\Auth;
use Illuminate\Http\Request;

class VerifyPhoneController extends Controller
{

    use Auth;

    private array $typeClassBinding = [
        'login' => [
            'class' => '\App\Http\Controllers\Auth\AuthenticatedSessionController',
            'method' => 'otpLogin',
            'requiresUserExistence' => true
        ],
        'register' => [
            'class' => '',
            'method' => '',
            'requiresUserExistence' => false
        ]
    ];


    public function send($type, SendRequest $request)
    {
        if ( $this->typeClassBinding[$type]['requiresUserExistence'] && !$this->doesUserExists($request) ) {
            return self::api(false, __('auth.login.username-exists'), [], 404);
        }

        return $request->sendOTP($type);
    }

    public function verify($type, VerifyRequest $request)
    {
        $req = $request->verifyOTP($type);

        if ( $req['ok'] ) {
            $bind = $this->typeClassBinding[$type];
            return $bind['class']::{$bind['method']}($request);
        }

        return $req;
    }

}
