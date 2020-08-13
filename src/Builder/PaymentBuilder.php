<?php

namespace Malico\MeSomb\Builder;

use Malico\MeSomb\Helper\PaymentData;
use Malico\MeSomb\Payment;

class PaymentBuilder
{
    use PaymentData;
    
    /**
     * Payment Owner Model
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $owner;

    public function __construct(
        $owner,
        $payer,
        $amount,
        $service = null,
        $currency = 'XAF',
        $fees = true,
        $message = null,
        $redirect = null
    ) {
        $this->owner = $owner;
        $this->payer = $payer;
        $this->amount = $amount;
        $this->service = $service;
        $this->fees = $fees;
        $this->currency = $currency;
        $this->message  = $message;
        $this->redirect = $redirect;
    }

    /**
     * Make Model Payment
     *
     * @return Malico\MeSomb\Model\Payment
     */
    public function pay()
    {
        $payment = (new Payment(
            $this->payer,
            $this->amount,
            $this->service,
            $this->fees,
            $this->currency,
            $this->message,
            $this->redirect
        ))->pay();

        $this->owner->payments()->save($payment);

        return $payment;
    }
}
