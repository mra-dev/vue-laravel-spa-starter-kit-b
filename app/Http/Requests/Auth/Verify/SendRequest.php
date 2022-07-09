<?php

namespace App\Http\Requests\Auth\Verify;

use App\Http\Traits\Auth;
use App\Http\Traits\Response;
use App\Models\OtpCode;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class SendRequest extends FormRequest
{

    use Auth, Response;

    /**
     * Maximum OTP Request per minute
     * @var int
     */
    private int $maxAttempts = 5;

    /**
     * Minutes gap between each OTP Request
     * @var int
     */
    private int $requestGap = 5;


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'regex:' . config('sms.regex')
            ]
        ];
    }


    public function sendOTP($type)
    {
        $this->requestedEarlier($type);
        $this->ensureIsNotRateLimited();

        $process = $this->sendVerificationSMS( $this->input('username'), $type );

        if ( !$process['ok'] ) {
            RateLimiter::hit($this->throttleKey());
            return $process;
        }

        RateLimiter::clear($this->throttleKey());
        return $process;
    }


    public function requestedEarlier($type)
    {
        $otpCode = OtpCode::where('phone', $this->input('username'))
            ->where('type', $type)
            ->whereColumn('expires_at', '<', 'updated_at')
            ->whereDate('updated_at', Carbon::now()->toDateString() )
            ->whereTime('updated_at', '>', Carbon::now()->subMinutes($this->requestGap)->toTimeString() );

        if ($otpCode->exists()) {
           throw ValidationException::withMessages([
               'token' => __('auth.sms.throttle')
           ]);
        }
    }

    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts( $this->throttleKey(), $this->maxAttempts ) ) {
            return;
        }

        event( new Lockout($this) );

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
        return $this->input('username') . '|' . $this->ip() . 'verify-send';
    }

}
