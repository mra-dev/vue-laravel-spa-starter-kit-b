<?php

namespace App\Http\Traits;

use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

trait Auth
{

    use Response;

    protected function send(): bool
    {
        return !app()->environment('local');
    }

    /**
     * @param $to
     * @param $type
     * @param string $title
     * @param string $gateway ['MeliPayamak', 'FarazSMS']
     * @param int $expiry token expire time (minutes)
     * @return array
     */
    private function sendVerificationSMS($to, $type, string $title = 'کاربر عزیز', string $gateway = 'FarazSMS', int $expiry = 3): array
    {
        $class = "\App\Http\Helpers\SMS\\" . $gateway;

        if ( !class_exists($class) )
            return self::arr(
                false,
                __('Defined SMS gateway not found.'),
                [],
                'warning'
            );

        $otp = OtpCode::
            where('phone', $to)
            ->where('type', $type)
            ->whereDate('expires_at', Carbon::now()->toDateString())
            ->whereTime('expires_at', '>', Carbon::now()->toTimeString());

        if ( $otp->exists() )
            return self::arr(
                true,
                __('A verification code already sent.'),
                [
                    'time_remaining' => $otp->first()->expires_at,
                    'again' => true
                ],
                'info'
            );

        $rand = rand(10000, 99999);

        if ( $this->send() ) {
            $rec_id = $class::sendVerifyPatternSMS([$title, $rand], $to);

            if ( $rec_id < 15 || !$class::isDelivered($rec_id) )
                return self::arr(
                    false,
                    __('Verification SMS not sent successfully.')
                );
        }

        $expires_at = Carbon::now()->addMinutes($expiry);
        OtpCode::updateOrCreate(
            [ 'phone' => $to ],
            [ 'code' => $rand, 'expires_at' => $expires_at, 'type' => $type ]
        );

        return self::arr(true, __('Verification code sent successfully.'), [
            'receipt_id' => $rec_id ?? null,
            'time_remaining' => $expires_at->toDateTimeString()
        ]);
    }

    private function verifyVerificationSMS($phone, $code, $type): array
    {
        $otp = OtpCode::
            where('phone', $phone)
            ->where('code', $code)
            ->where('type', $type)
            ->whereDate('expires_at', Carbon::now()->toDateString())
            ->whereTime('expires_at', '>', Carbon::now()->toTimeString());

        if ($otp->doesntExist())
            return self::arr(
                false,
                __('Entered verification code is invalid.'),
                [],
                'warning'
            );

        $otp->update([
            'expires_at' => Carbon::now()->subMinute()
        ]);

        return self::arr(
            true,
            __('Phone number verified successfully.')
        );
    }

    /**
     * @param $request
     * @return bool
     */
    public function doesUserExists($request): bool
    {
        return $this->getUserByName($request)->exists();
    }

    public function getUserByName($request)
    {
        return User::where('name', $request->input('username'));
    }

}
