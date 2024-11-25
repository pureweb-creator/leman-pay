<?php

namespace PurewebCreator\LemanPay\Request;

class CreatePaymentLinkResponse
{
    /**
     * @param object $payload
     */
    public function __construct(public object $payload)
    {}

    public function getOrderId()
    {
        return $this->payload->PaymentLinkId;
    }

    public function getPaymentLink()
    {
        return $this->payload->Uri;
    }
}