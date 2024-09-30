<?php

namespace PurewebCreator\LemanPay;

class MessageBuilder
{
    private string $message;

    public static function create(string $message): MessageBuilder
    {
        $builder = new self();
        $builder->message = $message;
        return $builder;
    }

    public function addRow(string $row): MessageBuilder
    {
        $this->message .= "<br>" . $row;
        return $this;
    }

    public function get(): string
    {
        return $this->message;
    }
}