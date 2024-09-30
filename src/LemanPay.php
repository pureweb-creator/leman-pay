<?php

namespace PurewebCreator\LemanPay;

use Exception;

class LemanPay implements PaymentGatewayInterface
{
    private const string ALG = "HS256";
    private const string HOST = "https://acsforpay.online";

    private string $sharedKey;
    private string $kid;
    private string $jws;
    private object $payment;

    public function __construct(
        private readonly PaymentService $paymentService = new PaymentService()
    )
    {}

    public function setSharedKey(string $key): void
    {
        $this->sharedKey = $key;
    }

    public function setKid(string $kid): void
    {
        $this->kid = $kid;
    }

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

    public function getPaymentLink(): string
    {
        return $this->payment->Uri ?? $this->payment->PaymentUrl;
    }

    public function getPaymentLinkId(): string
    {
        return $this->payment->PaymentLinkId ?? '';
    }

    /**
     * @throws Exception
     */
    public function createPaymentLink(array $payload): object
    {
        return $this->paymentRequest($payload, "/api/paymentlink/create");
    }

    /**
     * @throws Exception
     */
    public function pay(array $payload): static
    {
        return $this->paymentRequest($payload, "/api/cards/pay");
    }

    /**
     * @throws Exception
     */
    public function paymentLinkStatus(string $paymentId): object
    {
        $payload = ['PaymentLinkId' => $paymentId];

        return $this->paymentStatusRequest($payload, "/api/paymentlink/status");
    }

    /**
     * @throws Exception
     */
    public function transactionStatus(string $merchantId): object
    {
        $payload = ['MerchantId' => $merchantId];

        return $this->paymentStatusRequest($payload, "/api/transaction/status");
    }

    /**
     * @throws Exception
     */
    public function paymentStatusRequest(array $payload, string $path)
    {
        $this->jws = JWS::create($this->getProtectedHeader(), $payload, $this->sharedKey);

        $r = $this->paymentService->executeRequest(self::HOST.$path, $this->jws);

        return $this->paymentService->getParsedResponse($r);
    }

    /**
     * @throws Exception
     */
    public function paymentRequest(array $payload, string $path): object
    {
        $this->jws = JWS::create($this->getProtectedHeader(), $payload, $this->sharedKey);

        $r = $this->paymentService->executeRequest(self::HOST.$path, $this->jws);

        $payload = $this->paymentService->getParsedResponse($r);

        $this->payment = $payload;

        return $this;
    }
}