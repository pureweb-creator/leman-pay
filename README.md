# LemanPay

LemanPay is a PHP package that allows easy integration with the Leman payment gateway. You can either create payment links or directly process payments with credit cards. It is designed to be flexible, offering both an intuitive API and the ability to directly inspect the raw responses.

## Installation

You can install the package via Composer:

```bash
composer require pureweb-creator/leman-pay
```

## Setup

Before using the package, you need to set up some keys:

```php
<?php

use PurewebCreator\LemanPay\LemanPay;

$lemanPay = new LemanPay();
$lemanPay
    ->setSharedKey('your-shared-key')
    ->setKid('your-key-id');
```

## Usage Scenarios

### 1. Creating a Payment Link

You can generate a one-time payment link and redirect your user to that link for payment:

```php
<?php

$response = $lemanPay->createPaymentLink([
    "MerchantId" => "your-merchant-id", // required
    "Amount" => 1000, // required
    "Currency" => "RUB", // required (could be RUB, EUR, USD)
    "LinkType" => "OneTime", // required
    "ReturnUrl" => "http://127.0.0.1/return",
    "CallbackUrl" => "http://127.0.0.1/callback",
    "SuccessReturnUrl" => "http://127.0.0.1/success",
    "FailedReturnUrl" => "http://127.0.0.1/fail",
]);

// Get the payment link
$link = $response->getPaymentLink();

// Redirect the user to the payment link
header("Location: $link");
```

### Checking the Status of a Payment Link

After creating a payment link, you can check its status:

```php
<?php

$status = $lemanPay
    ->getPaymentLinkInfo('your-merchant-id') // the same MerchantId used in createPaymentLink()
    ->getStatus();

echo "Payment Link Status: " . $status;
```

The `Status` can be one of the following:

- `Completed`
- `Active`
- `Expired`
- `InProcess`
- `Cancelled`

### 2. Direct Payment (with Credit Card)

You can also process a direct payment using credit card details. The payment will typically require a redirect to a 3DS page for authentication:

```php
<?php

$response = $lemanPay->pay([
    "MerchantId" => "your-merchant-id", // required
    "Amount" => 1000, // required
    "Currency" => "RUB", // required (could be RUB, EUR, USD)
    "ReturnUrl" => "http://127.0.0.1/return2", // required
    "FromCard" => (object) [
        "Pan" => '4111111111111111', // card number
        "Holder" => 'NAME SURNAME', // cardholder name
        "ExpYear" => '2033', // expiration year (YYYY)
        "ExpMonth" => '06', // expiration month (MM)
        "CVV" => '123', // CVV code
    ]
]);

// Get the 3DS redirect link
$link = $response->getPaymentLink();

// Redirect to the 3DS page
header("Location: $link");
```

### Checking the Status of a Direct Payment

After processing the direct payment, you can check its status:

```php
<?php

$payment = $lemanPay->getPaymentInfo('your-merchant-id');
$paymentStatus = $payment->getOrder()->Status;

echo "Payment Status: " . $paymentStatus;
```

The `Order->Status` field can provide the current state of the transaction.

## Advanced: Direct Access to Response Fields

While you can use specific getters like `getStatus()` or `getPaymentLink()`, the entire response object is public, allowing you to directly inspect its fields:

```php
var_dump($response->body);
```
