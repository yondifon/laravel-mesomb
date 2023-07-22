<?php

namespace Malico\MeSomb\Helper;

use Malico\MeSomb\Builder\PaymentBuilder;

trait HasPayments
{
    /**
     * Model Payment.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function payments()
    {
        return $this->morphMany('Malico\MeSomb\Model\Payment', 'payable');
    }

    /**
     * Make Payment.
     *
     * @param  int|string  $payer
     * @param  float|int  $amount
     * @return Malico\MeSomb\Builder\PaymentBuilder
     */
    public function payment($payer = null, $amount = null)
    {
        return new PaymentBuilder($this, $payer, $amount);
    }
}
