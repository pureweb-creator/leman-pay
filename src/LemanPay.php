<?php

namespace PurewebCreator\LemanPay;

use Exception;
use PurewebCreator\LemanPay\Payment\LemanBase;

class LemanPay extends LemanBase
{
    /**
     * @throws Exception
     */
    public function pay(array $payload): object
    {
        return $this->createPayment($payload, self::DIRECT_PAYMENT_PATH);
    }

    /**
     * @throws Exception
     */
    public function createPaymentLink(array $payload): object
    {
        return $this->createPayment($payload, self::PAYMENT_LINK_PATH);
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
        return $this->info($paymentId);
    }
}
