<?php

namespace App\Http\Traits;

use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;

trait Auth
{

    protected function send()
    {
        return !app()->environment('local');
    }

    /**
     * @param $to
     * @param string $gateway ['MeliPayamak', 'FarazSMS']
     * @param int $expiry (minutes)
     * @return array
     */
    private function sendVerificationSMS($to, string $gateway = 'MeliPayamak', int $expiry = 2): array
    {
        $class = "\App\Http\Helpers\SMS\\" . $gateway;

        if (!class_exists($class))
            return [
                'ok' => false,
                'message' => __('Defined SMS gateway not found.')
            ];

        $otp = OtpCode::
            where('phone', $to)
            ->whereDate('expires_at', Carbon::now()->toDateString())
            ->whereTime('expires_at', '>', Carbon::now()->toTimeString());

        if ($otp->exists())
            return [
                'ok' => true,
                'message' => __('A verification code already sent.'),
                'data' => [
                    'time_remaining' => $otp->first()->expires_at,
                    'again' => true
                ]
            ];

        $rand = rand(10000, 99999);

        if ($this->send()) {
            $rec_id = $class::sendVerifyPatternSMS(['کاربر عزیز', $rand], $to);

            if ( $rec_id < 15 || !$class::isDelivered($rec_id) )
                return [
                    'ok' => false,
                    'message' => __('Verification SMS not sent successfully.')
                ];
        }

        $expires_at = Carbon::now()->addMinutes($expiry);
        OtpCode::updateOrCreate(
            [ 'phone' => $to ],
            [ 'code' => $rand, 'expires_at' => $expires_at ]
        );

        return [
            'ok' => true,
            'message' => __('Verification code sent successfully.'),
            'data' => [
                'receipt_id' => $rec_id ?? null,
                'time_remaining' => $expires_at->toDateTimeString()
            ]
        ];
    }

    private function verifyVerificationSMS($phone, $code): array
    {
        $otp = OtpCode::
            where('phone', $phone)
            ->where('code', $code)
            ->whereDate('expires_at', Carbon::now()->toDateString())
            ->whereTime('expires_at', '>', Carbon::now()->toTimeString());

        if ($otp->doesntExist())
            return [
                'ok' => false,
                'message' => __('Entered verification code is invalid.')
            ];

        $otp->update([
            'expires_at' => Carbon::now()->subMinute()
        ]);

        return [
            'ok' => true,
            'message' => __('Phone number verified successfully.')
        ];
    }

    private function isVerifyInProgress($registration = false): bool
    {
        $phone = session()->has('verify__phone');
        $code = session()->has('verify__code_expiry');
        $mode = session()->has('verify__mode');
        if ($phone && $code && $mode) {
            $now = Carbon::now();
            $expiry = Carbon::parse(session('verify__code_expiry'));
            $valid = $expiry->gt($now);
            $mode = session('verify__mode');
            $modeValid = ($registration && $mode == "register") || (!$registration && $mode == "login");

            if ($valid && $modeValid)
                return true;
        }

//        session()->forget([
//            'verify__phone', 'verify__code_expiry', 'verify__mode'
//        ]);

        return false;
    }

    private function isCompleteInProgress(): bool
    {
        $username = session()->has('register__username');
        $verified = session()->has('register__verified');
        $expiry = session()->has('register__expiry');
        $serial = session()->has('register__serial');

        if ($username && $verified && $expiry && $serial) {
            $now = Carbon::now();
            $expiry = Carbon::parse(session('register__expiry'));
            $valid = $expiry->gt($now);

            if ($valid)
                return true;
        }

        return false;
    }

    private function resetCompleteState()
    {
        session()->forget([
            'register__username', 'register__verified', 'register__expiry', 'register__serial'
        ]);
    }

}
