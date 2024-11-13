<?php

namespace PurewebCreator\LemanPay\Payment;

class PaymentResponse
{
    /**
     * @param object $payload
     */
    public function __construct(public object $payload)
    {}

    public function getOrderId()
    {
        return $this->payload->OrderId ?? $this->payload->PaymentLinkId;
    }

    public function getPaymentLink()
    {
        return $this->payload->PaymentUrl ?? $this->payload->Uri;
    }
}