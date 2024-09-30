<?php

namespace PurewebCreator\LemanPay;

class Utils
{
    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function getAlgo($algo): string
    {
        return match ($algo){
            'HS384' => 'sha384',
            'HS512' => 'sha512',
            default => 'sha256',
        };
    }
}