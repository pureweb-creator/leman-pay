<?php

namespace PurewebCreator\LemanPay\Payment;

class TransactionInfo
{
    /**
     * @param object $payload
     */
    public function __construct(public object $payload)
    {}

    public function getOrder(): object
    {
        return $this->payload->Order;
    }

    public function getRebillId(): string
    {
        return $this->payload->RebillId;
    }

    public function getRrn(): string
    {
        return $this->payload->Rrn;
    }

    public function getError(): string
    {
        return $this->payload->Error ?? false;
    }

    public function getCard(): object
    {
        return $this->payload->Card;
    }

    public function getClient(): object
    {
        return $this->payload->Client;
    }

    public function getBin(): object
    {
        return $this->payload->Bin;
    }

    public function getP2PPayee(): object
    {
        return $this->payload->P2PPayee;
    }

    public function getLinkedOrder(): array
    {
        return $this->payload->LinkedOrder;
    }
}