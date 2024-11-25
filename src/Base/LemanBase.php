<?php

namespace PurewebCreator\LemanPay\Base;

use PurewebCreator\LemanPay\Common\ResponseObject;
use PurewebCreator\LemanPay\Exception\BadApiRequestException;
use PurewebCreator\LemanPay\Exception\InvalidJwsException;
use PurewebCreator\LemanPay\Util\JWS;
use PurewebCreator\LemanPay\Util\MessageBuilder;

abstract class LemanBase
{
    /**
     * API base address
     */
    const string HOST = "https://acsforpay.online/api";

    /**
     * Prefix for all operations with transactions
     */
    const string TRANSACTION_PREFIX = "/transaction";

    /**
     * Prefix for all operations with cards
     */
    const string CARDS_PREFIX = "/cards";

    /**
     * Prefix for all operations with payment links
     */
    const string PAYMENTLINK_PREFIX = "/paymentlink";

    /**
     * Path for pay operation
     */
    const string PAY_PATH = "/pay";

    /**
     * Path for status operation
     */
    const string STATUS_PATH = "/status";

    /**
     * Path for creating payment link operation
     */
    const string CREATE_PATH = "/create";

    /**
     * Signing algorithm being used in JWS
     */
    const string ALG = "HS256";

    /**
     * It is used to sign the transmitted data
     * @var string
     */
    protected string $sharedKey;

    /**
     * Key ID
     * @var string
     */
    protected string $kid;

    protected function getProtectedHeader(): array
    {
        return [
            "alg" => self::ALG,
            "kid" => $this->kid,
            "signdate" => date('Y-m-d\TH:i:s.u\Z', time()),
            "cty" => "application/json"
        ];
    }

    public function setSharedKey(string $key): object
    {
        if (base64_encode(base64_decode($key, true)) === $key) {
            $this->sharedKey = base64_decode($key);
        } else {
            $this->sharedKey = $key;
        }

        return $this;
    }

    public function setKid(string $kid): object
    {
        $this->kid = $kid;

        return $this;
    }

    /**
     * @throws BadApiRequestException
     * @throws InvalidJwsException
     */
    public function createPayment(array $payload, string $path)
    {
        $jws = JWS::create($this->getProtectedHeader(), $payload, $this->sharedKey);

        $r = $this->execute(self::HOST.$path, $jws);

        $payment = $this->decodeData($r->getBody());

        if (isset($payment->Error)) {
            $this->handleError($payment->Error);
        }

        return $payment;
    }

    /**
     * @throws InvalidJwsException
     * @throws BadApiRequestException
     */
    public function getPaymentInfo(string $merchantId, string $path)
    {
        $jws = JWS::create(
            $this->getProtectedHeader(),
            ['MerchantId' => $merchantId],
            $this->sharedKey
        );

        $r = $this->execute(self::HOST.$path, $jws);

        $payment = $this->decodeData($r->getBody());

        if (isset($payment->Error)) {
            $this->handleError($payment->Error);
        }

        return $payment;
    }

    /**
     * @throws InvalidJwsException
     */
    protected function decodeData(bool|string $r): object
    {
        if (json_validate($r)) {
            return json_decode($r);
        }

        return json_decode(JWS::parse($r));
    }

    /**
     * @throws BadApiRequestException
     */
    protected function handleError($response): void
    {
        throw new BadApiRequestException(
            MessageBuilder::create("<b>$response->Code</b>")
                ->addRow($response->Message)
                ->build()
        );
    }

    protected function execute(string $uri, string $body): ResponseObject
    {
        $ch = curl_init($uri);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $body
        ]);

        $r = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return new ResponseObject(
            body: $r,
            status: $http_code
        );
    }
}