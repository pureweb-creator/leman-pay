<?php

namespace PurewebCreator\LemanPay\Util;

use Exception;

readonly class JWS
{
    public static function create(array $JWSHeader, array $JWSPayload, string $sharedKey): string
    {
        $computedHeader = Base64Url::encode(json_encode($JWSHeader));
        $computedPayload = Base64Url::encode(json_encode($JWSPayload));

        $JWSSignature = hash_hmac(
            self::matchAlgorithm($JWSHeader['alg']),
            $computedHeader.".".$computedPayload,
            $sharedKey,
            true);

        $computedSignature = Base64Url::encode($JWSSignature);

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

        return Base64Url::decode($payload);
    }

    public static function matchAlgorithm(string $alg): string
    {
        return match ($alg){
            'HS384' => 'sha384',
            'HS512' => 'sha512',
            default => 'sha256',
        };
    }
}