<?php

namespace PurewebCreator\LemanPay;

use GuzzleHttp\Exception\GuzzleException;
use PurewebCreator\LemanPay\Exception\BadApiRequestException;
use PurewebCreator\LemanPay\Exception\InvalidJwsException;
use PurewebCreator\LemanPay\Base\LemanBase;
use PurewebCreator\LemanPay\Request\CreatePaymentLinkResponse;
use PurewebCreator\LemanPay\Request\CreatePaymentResponse;
use PurewebCreator\LemanPay\Request\CreateRefundResponse;
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
     * @throws GuzzleException
     */
    public function pay(array $payload): CreatePaymentResponse
    {
        return $this->sendRequest(
            $payload,
            self::CARDS_PREFIX.self::PAY_PATH,
            CreatePaymentResponse::class
        );
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
     * @throws GuzzleException
     */
    public function createPaymentLink(array $payload): CreatePaymentLinkResponse
    {
        return $this->sendRequest(
            $payload,
            self::PAYMENTLINK_PREFIX.self::CREATE_PATH,
            CreatePaymentLinkResponse::class
        );
    }

    /**
     * @throws BadApiRequestException
     * @throws InvalidJwsException
     * @throws GuzzleException
     */
    public function getPaymentLinkInfo(string $merchantId): PaymentLinkResponse
    {
        return $this->sendRequest(
            ['MerchantId' => $merchantId],
            self::PAYMENTLINK_PREFIX.self::STATUS_PATH,
            PaymentLinkResponse::class);
    }

    /**
     * @throws BadApiRequestException
     * @throws InvalidJwsException
     * @throws GuzzleException
     */
    public function getTransactionInfo(string $merchantId): TransactionResponse
    {
        return $this->sendRequest(
            ['MerchantId' => $merchantId],
            self::TRANSACTION_PREFIX.self::STATUS_PATH,
        TransactionResponse::class);
    }

    /**
     * @throws InvalidJwsException
     * @throws GuzzleException
     * @throws BadApiRequestException
     */
    public function createRefund(array $payload): CreateRefundResponse
    {
        return $this->sendRequest(
            $payload,
            self::CARDS_PREFIX.self::REFUND_PATH,
            CreateRefundResponse::class
        );
    }
}
