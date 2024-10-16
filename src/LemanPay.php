<?php

namespace PurewebCreator\LemanPay;

use Exception;

class LemanPay extends LemanBase implements PaymentGatewayInterface
{
    /**
     * @throws Exception
     */
    public function pay(array $payload): object
    {
        return $this->paymentRequest($payload, self::DIRECT_PAYMENT_PATH, PaymentTypeEnum::DirectDebit);
    }

    /**
     * @throws Exception
     */
    public function createPaymentLink(array $payload): object
    {
        return $this->paymentRequest($payload, self::PAYMENT_LINK_PATH, PaymentTypeEnum::PaymentLink);
    }

    /**
     * @throws Exception
     */
    public function getCallbackInfo(string $payload): object
    {
        return $this->decodeData($payload);
    }

    /**
     * @throws Exception
     */
    public function getPaymentInfo(string $paymentId): object
    {
        return $this->info($paymentId, self::TRANSACTION_STATUS_PATH, PaymentTypeEnum::DirectDebit);
    }

    /**
     * @throws Exception
     */
    public function getPaymentLinkInfo(string $paymentId): object
    {
        return $this->info($paymentId, self::PAYMENT_LINK_STATUS_PATH, PaymentTypeEnum::PaymentLink);
    }
}
