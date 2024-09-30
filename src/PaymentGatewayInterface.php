<?php

namespace Aqua\LemanPay;

interface PaymentGatewayInterface
{
    public function createPaymentLink(array $payload);
    public function paymentLinkStatus(string $paymentId);
}