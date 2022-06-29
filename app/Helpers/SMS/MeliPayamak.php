<?php

namespace App\Http\Helpers\SMS;

use Melipayamak\MelipayamakApi;

class MeliPayamak
{

    private static function initSMSApi(): MelipayamakApi
    {
        $usr = config('sms.gateways.melipayamak.username');
        $pwd = config('sms.gateways.melipayamak.password');
        return new MelipayamakApi($usr, $pwd);
    }

    public static function sendSMS($text, $to, $from = false)
    {
        if (!$text || !$to)
            return null;

        $api = self::initSMSApi();
        $sms = $api->sms();
        $from = $from ?? config('sms.gateways.melipayamak.line');
        $response = $sms->send($to, $from, $text);

        return json_decode($response);
    }

    public static function sendPatternSMS(array $text, $to, $pattern_id)
    {
        if (!$text || !$to || !$pattern_id)
            return null;

        $api = self::initSMSApi();
        $sms = $api->sms('soap');
        $response = $sms->sendByBaseNumber($text, $to, $pattern_id);

        return json_decode($response);
    }

    public static function sendVerifyPatternSMS($text, $to)
    {
        if (!$text || !$to)
            return null;

        $text = is_array($text) ? $text : [$text];

        $pattern_id = config('sms.gateways.melipayamak.verify_pattern_id');

        return self::sendPatternSMS($text, $to, $pattern_id);
    }

    public static function isDelivered($rec_id): ?bool
    {
        if (!$rec_id)
            return null;

        $api = self::initSMSApi()
            ->sms()
            ->isDelivered($rec_id);
        $res = json_decode($api);
        return !!$res->RetStatus;
    }

}
