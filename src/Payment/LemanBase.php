<?php

namespace PurewebCreator\LemanPay\Payment;

use Exception;
use PurewebCreator\LemanPay\Exception\BadApiRequestException;
use PurewebCreator\LemanPay\Exception\InvalidRequestException;
use PurewebCreator\LemanPay\Util\JWS;
use PurewebCreator\LemanPay\Util\MessageBuilder;

abstract class LemanBase
{
    protected const string ALG = "HS256";
    protected const string HOST = "https://acsforpay.online";
    protected const string API_PREFIX = "/api";
    protected const string DIRECT_PAYMENT_PATH = self::API_PREFIX."/cards/pay";
    protected const string PAYMENT_LINK_PATH = self::API_PREFIX."/paymentlink/create";
    protected const string TRANSACTION_STATUS_PATH = self::API_PREFIX."/transaction/status";
    protected const string PAYMENT_LINK_STATUS_PATH = self::API_PREFIX."/paymentlink/status";

    protected string $sharedKey;
    protected string $kid;
    protected string $jws;

    public function __construct()
    {}

    /**
     * @throws Exception
     */
    private function getProtectedHeader(): array
    {
        $date = new \DateTime("now", new \DateTimeZone("UTC"));
        $formattedDate = $date->format("Y-m-d\TH:i:s.u\Z");
        $formattedDate = substr($formattedDate, 0, 23) . 'Z';

        return [
            "alg" => self::ALG,
            "kid" => $this->kid,
            "signdate" => $formattedDate,
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
     * @throws Exception
     */
    public function decodeData(bool|string $r): object
    {
        if (json_validate($r)) {
            $r = json_decode($r);

            throw new BadApiRequestException(
                MessageBuilder::create("Error")
                    ->addRow("Code: " . $r->Error->Code)
                    ->addRow("Message: " . $r->Error->Message)
                    ->build()
            );
        }

        $r = json_decode(JWS::parse($r));

        if (isset($r->Error)) {
            throw new InvalidRequestException(
                MessageBuilder::create("Error")
                    ->addRow("Code: " . $r->Error->Code)
                    ->addRow("Message: " . $r->Error->Message)
                    ->build()
            );
        }

        return $r;
    }

    /**
     * @throws Exception
     */
    public function paymentInfo(string $merchantId, string $path)
    {
        $this->jws = JWS::create(
            $this->getProtectedHeader(),
            ['MerchantId' => $merchantId],
            $this->sharedKey
        );

        $r = $this->execute(self::HOST.$path, $this->jws);

        return $this->decodeData($r);
    }

    /**
     * @throws Exception
     */
    public function getTransactionInfo(string $merchantId, string $path): TransactionInfo
    {
        $payload = $this->paymentInfo($merchantId, $path);

        return new TransactionInfo($payload);
    }

    /**
     * @throws Exception
     */
    public function getPaymentLinkInfo(string $merchantId, string $path): PaymentLinkInfo
    {
        $payload = $this->paymentInfo($merchantId, $path);

        return new PaymentLinkInfo($payload);
    }

    /**
     * @throws Exception
     */
    public function createPayment(array $payload, string $path): PaymentResponse
    {
        $this->jws = JWS::create($this->getProtectedHeader(), $payload, $this->sharedKey);

        $r = $this->execute(self::HOST.$path, $this->jws);

        $payload = $this->decodeData($r);

        return new PaymentResponse($payload);
    }

    public function execute(string $uri, string $body): bool|string
    {
        $ch = curl_init($uri);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $body
        ]);
        $r = curl_exec($ch);
        curl_close($ch);

        return $r;
    }
}