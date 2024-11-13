<?php

namespace PurewebCreator\LemanPay\Util;

class Base64Url
{
    public static function encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function decode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}