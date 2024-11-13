<?php

namespace PurewebCreator\LemanPay\Payment;

readonly class PaymentLinkInfo
{
    public function __construct(public object $body)
    {}

    public function getPaymentLink(): string
    {
        return $this->body->Uri;
    }

    public function getPaymentLinkId(): string
    {
        return $this->body->PaymentLinkId;
    }

    public function getMerchantId(): string
    {
        return $this->body->MerchantId;
    }

    public function getName(): string
    {
        return $this->body->Name;
    }

    public function getDescription(): string
    {
        return $this->body->Description;
    }

    public function getExpiryDate(): string
    {
        return $this->body->ExpiryDate;
    }

    public function getStatus(): string
    {
        return $this->body->Status;
    }

    public function getAmount(): int
    {
        return $this->body->Amount;
    }

    public function getCurrency(): string
    {
        return $this->body->Currency;
    }

    public function getPaymentMethod(): string
    {
        return $this->body->PaymentMethod;
    }

    public function getLinkType(): string
    {
        return $this->body->LinkType;
    }

    public function isEnabled(): bool
    {
        return $this->body->Enabled;
    }
}