<?php

namespace PurewebCreator\LemanPay\Base;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PurewebCreator\LemanPay\Exception\BadApiRequestException;
use PurewebCreator\LemanPay\Exception\InvalidJwsException;
use PurewebCreator\LemanPay\Util\JWS;
use PurewebCreator\LemanPay\Util\MessageBuilder;

abstract class LemanBase
{
    /**
     * API base address
     */
    const string HOST = "https://acsforpay.online/api";

    /**
     * Prefix for all operations with transactions
     */
    const string TRANSACTION_PREFIX = "/transaction";

    /**
     * Prefix for all operations with cards
     */
    const string CARDS_PREFIX = "/cards";

    /**
     * Prefix for all operations with payment links
     */
    const string PAYMENTLINK_PREFIX = "/paymentlink";

    /**
     * Path for pay operation
     */
    const string PAY_PATH = "/pay";

    /**
     * Path for status operation
     */
    const string STATUS_PATH = "/status";

    /**
     * Path for creating payment link operation
     */
    const string CREATE_PATH = "/create";

    /**
     * Path for refund operation
     */
    const string REFUND_PATH = "/refund";

    /**
     * Signing algorithm being used in JWS
     */
    const string ALG = "HS256";

    /**
     * It is used to sign the transmitted data
     * @var string
     */
    protected string $sharedKey;

    /**
     * Key ID
     * @var string
     */
    protected string $kid;

    public function __construct(protected ?ClientInterface $httpClient = null)
    {
        $this->httpClient = $this->httpClient ?? new Client([
            'base_uri' => self::HOST,
            'timeout'  => 2.0,
        ]);
    }

    protected function getHeaders(): array
    {
        return [
            "alg" => self::ALG,
            "kid" => $this->kid,
            "signdate" => date('Y-m-d\TH:i:s.u\Z', time()),
            "cty" => "application/json"
        ];
    }

    public function setSharedKey(string $key): object
    {
        if (base64_encode(base64_decode($key, true)) === $key) {
            $this->sharedKey = base64_decode($key);
            return $this;
        }

        $this->sharedKey = $key;
        return $this;
    }

    public function setKid(string $kid): object
    {
        $this->kid = $kid;

        return $this;
    }

    /**
     * @throws BadApiRequestException
     * @throws InvalidJwsException
     * @throws GuzzleException
     */
    protected function sendRequest(array $payload, string $path, string $responseClass): object
    {
        $jws = JWS::create($this->getHeaders(), $payload, $this->sharedKey);

        $r = $this->httpClient->request('POST', self::HOST.$path, [
            'body' => $jws,
        ]);

        $payment = $this->parseResponseBody($r->getBody());

        if (isset($payment->Error)) {
            $this->handleError($payment->Error);
        }

        return new $responseClass($payment);
    }

    /**
     * @throws InvalidJwsException
     */
    protected function parseResponseBody(string $r): object
    {
        return json_validate($r)
            ? json_decode($r)
            : json_decode(JWS::parse($r));
    }

    /**
     * @throws BadApiRequestException
     */
    protected function handleError($response): void
    {
        throw new BadApiRequestException(
            MessageBuilder::create("<b>$response->Code</b>")
                ->addRow((string)$response->Message)
                ->build()
        );
    }
}