<?php

namespace PurewebCreator\LemanPay\Util;

class MessageBuilder
{
    private string $message;

    public static function create(string $message): MessageBuilder
    {
        $builder = new self;
        $builder->message = $message;
        return $builder;
    }

    public function addRow(string $row): MessageBuilder
    {
        $this->message .= "<br>" . $row;
        return $this;
    }

    public function build(): string
    {
        return $this->message;
    }
}