<?php

namespace PurewebCreator\LemanPay;

use Exception;
use PurewebCreator\LemanPay\Exception\BadApiRequestException;
use PurewebCreator\LemanPay\Exception\InvalidJwsException;
use PurewebCreator\LemanPay\Base\LemanBase;
use PurewebCreator\LemanPay\Request\CreatePaymentLinkResponse;
use PurewebCreator\LemanPay\Request\CreatePaymentResponse;
use PurewebCreator\LemanPay\Request\PaymentLinkResponse;
use PurewebCreator\LemanPay\Request\TransactionResponse;

class LemanPay extends LemanBase
{
    /**
     * Payment creation via direct debit
     *
     * @param array $payload
     *
     * @return CreatePaymentResponse
     *
     * @throws InvalidJwsException Invalid JWS format
     * @throws BadApiRequestException Invalid request. Most often, this status is issued due to a violation of the rules for interacting with the API.
     */
    public function pay(array $payload): CreatePaymentResponse
    {
        $payment = $this->createPayment($payload, self::CARDS_PREFIX.self::PAY_PATH);

        return new CreatePaymentResponse($payment);
    }

    /**
     * Payment creation via a payment link
     *
     * @param array $payload
     *
     * @return CreatePaymentLinkResponse
     *
     * @throws InvalidJwsException Invalid JWS format
     * @throws BadApiRequestException Invalid request. Most often, this status is issued due to a violation of the rules for interacting with the API.
     */
    public function createPaymentLink(array $payload): CreatePaymentLinkResponse
    {
        $payment = $this->createPayment($payload, self::PAYMENTLINK_PREFIX.self::CREATE_PATH);

        return new CreatePaymentLinkResponse($payment);
    }

    /**
     * @throws BadApiRequestException
     * @throws InvalidJwsException
     */
    public function getPaymentLinkInfo(string $merchantId): PaymentLinkResponse
    {
        $payload = $this->getPaymentInfo($merchantId, self::PAYMENTLINK_PREFIX.self::STATUS_PATH);

        return new PaymentLinkResponse($payload);
    }

    /**
     * @throws BadApiRequestException
     * @throws InvalidJwsException
     */
    public function getTransactionInfo(string $merchantId): TransactionResponse
    {
        $payload = $this->getPaymentInfo($merchantId, self::TRANSACTION_PREFIX.self::STATUS_PATH);

        return new TransactionResponse($payload);
    }
}
