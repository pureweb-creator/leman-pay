<?php

namespace PurewebCreator\LemanPay\Request;

class CreateRefundResponse extends AbstractResponse
{
    public function getOrderId()
    {
        return $this->body->OrderId;
    }

    public function getMerchantId()
    {
        return $this->body->MerchantId;
    }
}