<?php

namespace PurewebCreator\LemanPay\Request;

class CreatePaymentResponse extends AbstractResponse
{
    public function getOrderId()
    {
        return $this->body->OrderId;
    }

    public function getPaymentLink()
    {
        return $this->body->PaymentUrl;
    }
}