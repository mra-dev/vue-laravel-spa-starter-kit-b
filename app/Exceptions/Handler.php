<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {

        $this->renderable(function (ThrottleRequestsException $e, Request $request) {
            if ( $request->expectsJson() ) {
                return response()->json([
                    'ok' => false,
                    'message' => __("Too Many Attempts."),
                    'data' => [
                        'throttle_message' => __('auth.throttle', ['seconds' => $e->getHeaders()['Retry-After']]),
                        'throttle_attempts_limit' => $e->getHeaders()['X-RateLimit-Limit'],
                        'throttle_attempts_left' => $e->getHeaders()['X-RateLimit-Remaining'],
                        'throttle_time_left' => $e->getHeaders()['Retry-After'],
                        'throttle_time_reset' => Carbon::createFromTimestamp($e->getHeaders()['X-RateLimit-Reset'])
                                                    ->toTimeString()
                    ]
                ], 429, $e->getHeaders());
            }
        });

    }
}
