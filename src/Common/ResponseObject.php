<?php

namespace PurewebCreator\LemanPay\Common;

class ResponseObject
{
    public function __construct(
        protected mixed $body,
        protected string|int $status,
    )
    {}

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}