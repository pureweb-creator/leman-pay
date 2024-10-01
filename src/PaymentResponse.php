<?php

namespace PurewebCreator\LemanPay;

readonly class PaymentResponse
{
    public function __construct(public object $body)
    {}

    public function getPaymentLink(): string
    {
        return $this->body->PaymentUrl;
    }

    public function getOrder(): object
    {
        return $this->body->Order;
    }

    public function getRebillId(): string
    {
        return $this->body->RebillId;
    }

    public function getRrn(): string
    {
        return $this->body->Rrn;
    }

    public function getError(): string
    {
        return $this->body->Error ?? false;
    }

    public function getCard(): object
    {
        return $this->body->Card;
    }

    public function getClient(): object
    {
        return $this->body->Client;
    }

    public function getBin(): object
    {
        return $this->body->Bin;
    }

    public function getP2PPayee(): object
    {
        return $this->body->P2PPayee;
    }

    public function getLinkedOrder(): array
    {
        return $this->body->LinkedOrder;
    }
}