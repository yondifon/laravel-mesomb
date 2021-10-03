<?php

namespace Malico\MeSomb\Helper;

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasTransactions
{
    /**
     * Deposit|Payment Transaction.
     */
    public function transaction(): MorphOne
    {
        return $this->morphOne('Malico\MeSomb\Model\Transaction', 'transacable');
    }

    /**
     * Succesful Transactoin.
     */
    public function toggleToSuccess(): void
    {
        $this->update(['success' => true]);

        $this->save();
    }
}
