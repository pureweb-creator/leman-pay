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
    ->setSharedKey("your-shared-key")
    ->setKid("your-key-id");
```

## Usage Scenarios

### 1. Creating a Payment Link

You can generate a one-time payment link and redirect your user to that link for payment:

```php
<?php

$response = $lemanPay->createPaymentLink([
    "MerchantId" => "12323", // Required
    "Name" => "TestLink",
    "Amount" => 1000, // Required
    "Currency" => "RUB", // Required
    "LinkType" => "OneTime", // Required
    "PaymentMethod" => "CARDS",
    "TTL" => "00:30:00",
    "ReturnUrl" => "https://site.com/return",
    "CallbackUrl" => "https://webhook.site/notification",
    "SuccessReturnUrl" => "https://site.com/success",
    "FailedReturnUrl" => "https://site.com/failure",
    "AdditionalData" => (object) [
        "ClientInfo" => (object) [ // Required if PaymentMethod  = P2PPAY
            "ClientId" => "12323", // Required
            "PhoneNumber" => "+79991231212",
            "FirstName" => "John",
            "LastName" => "Doe",
            "DateOfBirth" => "2022-08-09T10:55:42.8017883Z",
            "Email" => "john.doe@site.com", // Required
            "Country" => "UKR",
            "State" => "Kyiv Oblast",
            "City" => "Kyiv",
            "PostCode" => "01001",
            "Address" => "20 Sunny Street"
        ],
    ],
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
    ->getPaymentLinkInfo("your-merchant-id") // the same MerchantId used in createPaymentLink()
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
    "MerchantId" => "12323", // Required
    "Amount" => 1000, // Required
    "Currency" => "RUB", // Required
    "ReturnUrl" => "https://site.com/", // Required
    "CallbackUrl" => "https://webhook.site/notification",
    "ClientInfo" => (object) [
        "ClientId" => "12323",
        "PhoneNumber" => "+79991231212",
        "FirstName" => "John",
        "LastName" => "Doe",
        "DateOfBirth" => "2022-08-09T10:55:42.8017883Z",
        "Email" => "john.doe@site.com",
        "Country" => "UKR",
        "State" => "Kyiv Oblast",
        "City" => "Kyiv",
        "PostCode" => "01001",
        "Address" => "20 Sunny Street"
    ],
    "BrowserInfo" => (object) [
        "AcceptHeader" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "ScreenWidth" => 1536,
        "ScreenHeight" => 864,
        "ScreenColorDepth" => 24,
        "WindowWidth" => 1536,
        "WindowHeight" => 738,
        "Language" => "en-US",
        "JavaEnabled" => false,
        "UserAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36",
        "TimeZone" => -180,
    ],
    "Description" => "Test transaction",
    "FromCard" => (object) [ // Required
        "Pan" => "4111111111111111",
        "Holder" => "NAME SURNAME", 
        "ExpYear" => "2033", 
        "ExpMonth" => "06", 
        "CVV" => "123",
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

$payment = $lemanPay->getPaymentInfo("your-merchant-id");
$paymentStatus = $payment->getOrder()->Status;

echo "Payment Status: " . $paymentStatus;
```

The `Order->Status` field can provide the current state of the transaction.

## Receive webhook

###  Why use webhooks
This enables your system to respond to events immediately, such as updating an order status as soon as a payment is confirmed, without needing to poll for updates.

### Setting up the webhook url
This is done by setting the "CallbackUrl" field in the payload when you create a payment request.

### Example: Handling Webhooks
```php
<?php
// Retrieve the webhook data from the request
$callback = @file_get_contents('php://input');

try {
    // Process the callback data using LemanPay
    $callback = $lemanPay->getCallbackInfo($callback);

} catch (Exception $e) {
    // Respond with a 400 status code if there is an error processing the webhook
    http_response_code(400);
    exit;
}

// Handle the event based on the order status
switch ($callback->Payload->Order->Status) {
    case 'Completed':
        // Payment was successful, handle accordingly
        $order = $callback->Payload->Order;
        // todo: handle successful payment, e.g., update order status in the database
        break;

    case 'Cancelled':
        // Payment was cancelled, handle accordingly
        // todo: handle cancelled payment, e.g., notify the user
        break;

    default:
        // If an unknown status is received, return an appropriate message
        echo 'Unknown error';
}
```

## Advanced: Direct Access to Response Fields

While you can use specific getters like `getStatus()` or `getPaymentLink()`, the entire response object is public, allowing you to directly inspect its fields:

```php
var_dump($response->body);
```
