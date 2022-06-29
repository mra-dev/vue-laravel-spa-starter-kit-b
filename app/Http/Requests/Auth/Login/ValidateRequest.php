<?php

namespace App\Http\Requests\Auth\Login;

use Illuminate\Foundation\Http\FormRequest;

class ValidateRequest extends FormRequest
{
    //    private int $maxAttempts = 1;
//    private string $field = 'username';

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
            ]
        ];
    }

    public function messages()
    {
        return [
            'username.required' => __('Entering phone number is required.'),
            'username.regex' => __('Entered phone number is invalid.')
        ];
    }

    public function attributes()
    {
        return [
            'username' => "شماره همراه"
        ];
    }


    public function process()
    {
//        $this->ensureIsNotRateLimited();
//        RateLimiter::hit($this->throttleKey());

//        RateLimiter::clear($this->throttleKey());
    }


    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
//    public function ensureIsNotRateLimited()
//    {
//        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxAttempts)) {
//            return;
//        }
//
//        event(new Lockout($this));
//
//        $seconds = RateLimiter::availableIn($this->throttleKey());
//
//        throw ValidationException::withMessages([
//            $this->field => trans('auth.throttle', [
//                'seconds' => $seconds,
//                'minutes' => ceil($seconds / 60),
//            ]),
//        ]);
//    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
//    public function throttleKey(): string
//    {
//        return $this->input($this->field) . '|' . $this->ip();
//    }
}
