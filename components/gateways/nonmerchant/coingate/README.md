# CoinGate PHP library

PHP library for CoinGate API.

You can sign up for a CoinGate account at <https://coingate.com> for production and <https://sandbox.coingate.com> for testing (sandbox).

Please note, that for Sandbox you must generate separate API credentials on <https://sandbox.coingate.com>. API credentials generated on <https://coingate.com> will not work for Sandbox mode.

## Composer

You can install library via [Composer](http://getcomposer.org/). Run the following command in your terminal:

```bash
composer require blesta/coingate
```

## Manual Installation

Donwload [latest release](https://github.com/blesta/gateway-coingate/releases) and include `init.php` file.

```php
require_once('/path/to/coingate/init.php');
```

## Getting Started

Usage of CoinGate library.

### Setting up CoinGate library

#### Setting default authentication

```php
use CoinGate\CoinGate;

\CoinGate\CoinGate::config(array(
  'environment' => 'sandbox', // sandbox OR live
  'app_id'      => 'YOUR_APP_ID',
  'api_key'     => 'YOUR_API_KEY',
  'api_secret'  => 'YOUR_API_SECRET'
));

// $order = \CoinGate\Merchant\Order::find(7294);
```

#### Setting authentication individually

```php
use CoinGate\CoinGate;

# \CoinGate\Merchant\Order::find($orderId, $options = array(), $authentication = array())

$order = \CoinGate\Merchant\Order::find(1087999, array(), array(
    'environment' => 'sandbox', // sandbox OR live
    'app_id' => 'YOUR_APP_ID',
    'api_key' => 'YOUR_API_KEY',
    'api_secret' => 'YOUR_API_SECRET'));
```

### Creating Merchant Order

https://developer.coingate.com/docs/create-order

```php
use CoinGate\CoinGate;

$post_params = array(
                   'order_id'          => 'YOUR-CUSTOM-ORDER-ID-115',
                   'price'             => 1050.99,
                   'currency'          => 'USD',
                   'receive_currency'  => 'EUR',
                   'callback_url'      => 'https://example.com/payments/callback?token=6tCENGUYI62ojkuzDPX7Jg',
                   'cancel_url'        => 'https://example.com/cart',
                   'success_url'       => 'https://example.com/account/orders',
                   'title'             => 'Order #112',
                   'description'       => 'Apple Iphone 6'
               );

$order = \CoinGate\Merchant\Order::create($post_params);

if ($order) {
    echo $order->status;
} else {
    # Order Is Not Valid
}
```

### Getting Merchant Order

https://developer.coingate.com/docs/get-order

```php
use CoinGate\CoinGate;

try {
    $order = \CoinGate\Merchant\Order::find(7294);

    if ($order) {
      var_dump($order);
    }
    else {
      echo 'Order not found';
    }
} catch (Exception $e) {
  echo $e->getMessage(); // BadCredentials Not found App by Access-Key
}
```

### Test API Credentials

```php
$testConnection = \CoinGate\CoinGate::testConnection(array(
  'environment'   => 'sandbox',
  'app_id'        => 'APP_ID',
  'api_key'       => 'APP_KEY',
  'api_secret'    => 'APP_SECRET'
));

if ($testConnection !== true) {
  echo $testConnection; // CoinGate\BadCredentials: BadCredentials Not found App by Access-Key
}
```
