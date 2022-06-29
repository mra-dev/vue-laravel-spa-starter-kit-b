<?php

namespace App\Http\Helpers\SMS;

use IPPanel\Client;
use IPPanel\Errors\Error;
use IPPanel\Errors\HttpException;

class FarazSMS
{

    private static function initClient(): Client
    {
        $apiKey = config('sms.gateways.farazsms.apikey');
        return new Client($apiKey);
    }

    public static function sendSMS($text, $to, $from = null): int|string|null
    {
        if (!$text || !$to)
            return null;

        $client = self::initClient();
        $to = is_array($to) ? $to : [$to];
        $from = $from ?? config('sms.gateways.farazsms.sms_line');
        try {
            return $client->send($from, $to, $text);
        } catch (Error|HttpException $e) {
            return $e->getMessage();
        }
    }

    public static function sendPatternSMS($values, $to, $pattern_id, $from = null): int|string|null
    {
        if (!$values || !$to || !$pattern_id)
            return null;

        $client = self::initClient();
        $from = $from ?? config('sms.gateways.farazsms.pattern_line');

        try {
            return $client->sendPattern($pattern_id, $from, $to, $values);
        } catch (Error|HttpException $e) {
            return $e->getMessage();
        }
    }

    public static function sendVerifyPatternSMS($values, $to): int|string|null
    {
        if (!$values || !$to)
            return null;

        $values = [
            'name' => $values[0],
            'code' => (string) $values[1]
        ];
        $pattern_id = config('sms.gateways.farazsms.verify_pattern_id');

        return self::sendPatternSMS($values, $to, $pattern_id);
    }

    public static function isDelivered($rec_id, $real = false): bool|string|null
    {
        if (!$rec_id)
            return null;

        $client = self::initClient();

        try {
            return $client->getMessage($rec_id)->status === ($real ? 'finish' : 'active');
        } catch (Error|HttpException $e) {
            return $e->getMessage();
        }
    }

}
