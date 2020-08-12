use Malico\MeSomb\Helper\HasPayments;

# Laravel MeSomb

Laravel Wrapper on top of MeSomb Payment API

## Roadmap

API Features and their implementations [https://mesomb.hachther.com/en/api/schema/](https://mesomb.hachther.com/en/api/schema/)

| Feature              | Status  |
| -------------------- | ------- |
| Payment              | &#9745; |
| Transaction Status   | &#9745; |
| Application Status   | &#9744; |
| Deposits             | &#9744; |
| Test                 | &#9744; |
| Better Documentation | &#9744; |

## Installation

Install Package

```shell
composer require malico/laravel-mesomb
```

Publish Configuration Files

```shell
php artisan vendor:publish --tag=mesomb-configuration
```

Sign up and Create new Application at [https://mesomb.hachther.com/](https://mesomb.hachther.com/). Provide appropriate from your dashboard configure for the `config/mesomb.php`;

```php
<?php

return [

    /**
     * Api Version
     *
     * @var string
     */
    'version' => 'v1.0',

    /**
     * MeSomb Application Key
     * Copy from https://mesomb.hachther.com/en/applications/{id}
     *
     * @var string
     */
    'key' => env('MeSomb_APP_KEY'),

    /**
     * MeSomb API Application Key
     * Copy from https://mesomb.hachther.com/en/applications/{id}
     *
     * @var string
     */
    'api_key' => env('MeSomb_API_KEY'),

    /**
     * PIN used for MeSomb Pin
     * Configure @ https://mesomb.hachther.com/en/applications/{id}/settings/setpin/
     *
     * @var int|string
     */
    'pin' => env('MeSomb_PIN', null),

    /**
     * Supported Payment Methods
     *
     * @var array
     */
    'currencies' => ['XAF', 'XOF'],

    /**
     * Support Payment Methods
     * Array in order of preference
     *
     * @var array
     */
    'services' => ['MTN', 'ORANGE'],

    /**
     * Payments
     */
    'payable' => [
        /**
         * Set this true if you're using uuid insteads of auto-increments  for id
         *
         * @var bool
         */
        'uuid' => true
    ],

    /**
     * Failed Payments
     *
     * @var array
     */
    'failed_payments' => [
        /**
         * Add Failed requests to queue ( to check transactions)
         *
         * @var bool
         */
        'check' => false,
    ]
];
```

Migrate Mesomb Transaction Tables

```shell
php artisan migrate
```

## Usage

### Payments

Examples

1. Simple Payments

    ```php
    // OrderController.php
    use Malico\MeSomb\Payment;

    class OrderController extends Controller {

        public function confirmOrder()
        {
            $request = new Payment('+23767xxxxxxx', 1000);

            $payment = $request->pay();

            if($payment->success){
                // Fire some event,Pay someone, Alert user
            } else {
                // fire some event, redirect to error page
            }

            // get Transactions details $payment->transactions
        }
    }
    ```

2. Attaching Payments to Models Directly

    ```php

    // Order.php

    use Malico\MeSomb\Helper\HasPayments;

    class Order extends Model
    {
        use HasPayments;
    }

    // OrderController.php

    class OrderController extends Controller {

        public function confirmOrder(){

            $order = Order::create(['amount' => 100]);

            $payment  = $order->payment('+23767xxxxxxx', $order->amount)->pay();

            if($payment->success){
                // Fire some event,Pay someone, Alert user
            } else {
                // fire some event, redirect to error page
            }

            // View Order payments via $order->payments

            // Get payment transaction with $payment->transaction

            return $payment;
        }
    }
    ```

### Transactions

Check

#### Author

Malico (Desmond Yong)
[hi@malico.me](hi@malico.me)

```

```
