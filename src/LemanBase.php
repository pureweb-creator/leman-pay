<?php

namespace PurewebCreator\LemanPay;

use Exception;

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

            throw new Exception(
                MessageBuilder::create("Error")
                    ->addRow("Code: " . $r->Error->Code)
                    ->addRow("Message: " . $r->Error->Message)
                    ->get()
            );
        }

        $r = json_decode(JWS::parse($r));

        if (isset($r->Error)) {
            throw new Exception(
                MessageBuilder::create("Error")
                    ->addRow("Code: " . $r->Error->Code)
                    ->addRow("Message: " . $r->Error->Message)
                    ->get()
            );
        }

        return $r;
    }


    /**
     * @throws Exception
     */
    public function info(string $paymentId, string $path, $paymentType): PaymentResponse|PaymentLinkResponse
    {
        $payload = ['MerchantId' => $paymentId];

        $this->jws = JWS::create($this->getProtectedHeader(), $payload, $this->sharedKey);

        $r = $this->execute(self::HOST.$path, $this->jws);

        $payload = $this->decodeData($r);

        return match ($paymentType) {
            PaymentTypeEnum::DirectDebit =>  new PaymentResponse($payload),
            PaymentTypeEnum::PaymentLink => new PaymentLinkResponse($payload)
        };
    }

    /**
     * @throws Exception
     */
    public function paymentRequest(array $payload, string $path, $paymentType): PaymentResponse|PaymentLinkResponse
    {
        $this->jws = JWS::create($this->getProtectedHeader(), $payload, $this->sharedKey);

        $r = $this->execute(self::HOST.$path, $this->jws);

        $payload = $this->decodeData($r);

        return match ($paymentType) {
            PaymentTypeEnum::DirectDebit =>  new PaymentResponse($payload),
            PaymentTypeEnum::PaymentLink => new PaymentLinkResponse($payload)
        };
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