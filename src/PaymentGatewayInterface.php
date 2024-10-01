<?php

namespace PurewebCreator\LemanPay;

interface PaymentGatewayInterface
{
    public function createPaymentLink(array $payload);
    public function getPaymentInfo(string $paymentId);
}