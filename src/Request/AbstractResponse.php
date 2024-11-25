<?php

namespace PurewebCreator\LemanPay\Request;

class AbstractResponse
{
    public function __construct(public object $body)
    {}
}