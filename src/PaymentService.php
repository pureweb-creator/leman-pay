<?php

namespace PurewebCreator\LemanPay;

use Exception;

class PaymentService
{
    public function executeRequest(string $uri, string $body): bool|string
    {
        $ch = curl_init($uri);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $body
        ]);
        $r = curl_exec($ch);
        curl_close($ch);

        return $r;
    }

    /**
     * @throws Exception
     */
    public function getParsedResponse(bool|string $r): object
    {
        if (json_validate($r)) {
            $r = json_decode($r);
            throw new Exception(
                MessageBuilder::create("Error")
                    ->addRow("Code: " . $r->Error->Code)
                    ->addRow("Message: " . $r->Error->Message)
                    ->get()
            );
        }

        $r = JWS::parse($r);

        if (isset($r->Error)) {
            throw new Exception(
                MessageBuilder::create("Error")
                    ->addRow("Code: " . $r->Error->Code)
                    ->addRow("Message: " . $r->Error->Message)
                    ->get()
            );
        }

        return $r;
    }
}