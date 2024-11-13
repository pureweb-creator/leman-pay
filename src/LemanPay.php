<?php

namespace PurewebCreator\LemanPay;

use Exception;
use PurewebCreator\LemanPay\Payment\LemanBase;
use PurewebCreator\LemanPay\Payment\PaymentLinkInfo;
use PurewebCreator\LemanPay\Payment\TransactionInfo;

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
    public function getPaymentInfo(string $merchantId): PaymentLinkInfo|TransactionInfo
    {
        try {
            $paymentInfo = $this->getTransactionInfo($merchantId, self::TRANSACTION_STATUS_PATH);
        } catch (Exception $exception) {
            $paymentInfo = $this->getPaymentLinkInfo($merchantId, self::PAYMENT_LINK_STATUS_PATH);
        }

        return $paymentInfo;
    }
}
