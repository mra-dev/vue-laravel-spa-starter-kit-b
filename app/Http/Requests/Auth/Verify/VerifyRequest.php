<?php

namespace App\Http\Requests\Auth\Verify;

use App\Http\Traits\Auth;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VerifyRequest extends FormRequest
{

    use Auth;

    private int $maxAttempts = 5;


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => [
                'required',
                'regex:' . config('sms.regex')
            ],
            'token' => [
                'required',
                'integer'
            ]
        ];
    }

    public function verifyOTP($type): array
    {
        $this->ensureIsNotRateLimited();

        $process = $this->verifyVerificationSMS( $this->input('username'), $this->input('token'), $type );

        if ( !$process['ok'] ) {
            RateLimiter::hit($this->throttleKey());
            return $process;
        }

        RateLimiter::clear($this->throttleKey());
        return $process;
    }

    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts( $this->throttleKey(), $this->maxAttempts ) ) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn( $this->throttleKey() );

        throw ValidationException::withMessages([
            'token' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60)
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return $this->input('username') . '|' . $this->ip() . 'verify';
    }

}
