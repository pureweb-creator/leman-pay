<?php

namespace PurewebCreator\LemanPay;

use Exception;

readonly class JWS
{
    public static function create(array $JWSHeader, array $JWSPayload, string $sharedKey): string
    {
        $computedHeader = Utils::base64UrlEncode(json_encode($JWSHeader));
        $computedPayload = Utils::base64UrlEncode(json_encode($JWSPayload));

        $JWSSignature = hash_hmac(
            Utils::getAlgo($JWSHeader['alg']),
            $computedHeader.".".$computedPayload,
            $sharedKey,
            true);

        $computedSignature = Utils::base64UrlEncode($JWSSignature);

        return $computedHeader . '.' . $computedPayload . '.' . $computedSignature;
    }

    /**
     * @throws Exception
     */
    public static function parse(string $jws): string
    {
        $parts = explode(".", $jws);

        if (count($parts) !== 3) {
            throw new Exception("Invalid JWS format. Expecting header, payload, and signature.");
        }

        list(, $payload, ) = $parts;

        return Utils::base64UrlDecode($payload);
    }
}