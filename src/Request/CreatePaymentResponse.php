<?php

namespace PurewebCreator\LemanPay\Request;

class CreatePaymentResponse
{
    /**
     * @param object $payload
     */
    public function __construct(public object $payload)
    {}

    public function getOrderId()
    {
        return $this->payload->OrderId;
    }

    public function getPaymentLink()
    {
        return $this->payload->PaymentUrl;
    }
}