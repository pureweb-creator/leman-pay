<?php

namespace PurewebCreator\LemanPay\Request;

class CreatePaymentLinkResponse extends AbstractResponse
{
    public function getOrderId()
    {
        return $this->body->PaymentLinkId;
    }

    public function getPaymentLink()
    {
        return $this->body->Uri;
    }
}