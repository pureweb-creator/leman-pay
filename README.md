# LemanPay

LemanPay is a PHP package that makes it simple to integrate LemanPay Payment Gateway functionality into your applications. It supports both redirect-based payment flows and direct card payments.

## Installation

Install the package via Composer:

```bash
composer require pureweb-creator/leman-pay
```

## Setup

Before using the package, you need to set up your shared key and key ID (kid):

```php
<?php

use PurewebCreator\LemanPay\LemanPay;

$lemanPay = new LemanPay();
$lemanPay->setSharedKey('your-shared-key');
$lemanPay->setKid('your-key-id');
?>
```

## Usage

There are two primary scenarios for using LemanPay:

### 1. Redirect to Payment Link

In this scenario, you create a payment link that redirects the user to complete the payment.

#### Create a Payment Link

```php
<?php
$payment = $lemanPay->createPaymentLink([
    "MerchantId" => "11111111", // Unique payment link ID assigned by the merchant (required)
    "Amount" => 1000,           // Payment amount (required)
    "Currency" => "RUB",        // Payment currency (required, could be RUB, EUR, USD)
    "LinkType" => "OneTime",    // Type of payment link (required)
    "ReturnUrl" => "http://test.com/return",
    "CallbackUrl" => "http://test.com/callback",
    "SuccessReturnUrl" => "http://test.com/success",
    "FailedReturnUrl" => "http://test.com/fail",
]);
?>
```

#### Get and Redirect to Payment Link

Once the payment link is created, you can get the payment URL and redirect the user:

```php
<?php
$link = $payment->getPaymentLink();
header("Location: $link");
?>
```

#### Check Payment Status

To check the status of a payment:

```php
<?php
$payment = $lemanPay->paymentLinkStatus($merchantId);

switch ($payment->Status) {
    case 'Completed':
        echo "Payment completed.";
        break;
    case 'Active':
        echo "Payment is still active.";
        break;
    case 'Expired':
        echo "Payment link has expired.";
        break;
    case 'InProcess':
        echo "Payment is in process.";
        break;
    case 'Cancelled':
        echo "Payment was cancelled.";
        break;
}
?>
```

Here, `merchantId` is the unique ID you used when creating the payment link.

### 2. Direct Card Payment

In this scenario, the user can pay directly with their credit card details.

#### Make a Direct Card Payment

```php
<?php
$payment = $lemanPay->pay([
    "MerchantId" => "11111111", // Unique merchant ID (required)
    "Amount" => 1000,           // Payment amount (required)
    "ReturnUrl" => "http://test.com/return",
    "Currency" => "RUB",        // Payment currency (required, can be RUB, USD, EUR)
    "FromCard" => (object) [
        "Pan" => '1111111111111111', // Card number
        "Holder" => 'NAME SURNAME',  // Cardholder name
        "ExpYear" => '2033',         // Expiration year (4 digits)
        "ExpMonth" => '06',          // Expiration month (2 digits)
        "CVV" => '111',              // CVV code (3 digits)
    ]
]);
?>
```

#### Get Payment Link for Direct Card Payment

This link is the URL address that the merchant needs to go to in order to continue the payment process

```php
<?php
$link = $payment->getPaymentLink();
?>
```

#### Check Transaction Status

To check the status of a transaction:

```php
<?php
$payment = $lemanPay->transactionStatus($merchantId);

if ($payment->Order->Status === 'Completed') {
    echo "Transaction completed.";
} else {
    echo "Transaction status: " . $payment->Order->Status;
}
?>
```

Here, `merchantId` is the unique ID you used when creating the payment.

## License

This package is licensed under the MIT License.

---

This structure includes installation, setup, both usage scenarios, and how to check payment statuses. You can extend it by adding more detailed descriptions or additional features your package might support!