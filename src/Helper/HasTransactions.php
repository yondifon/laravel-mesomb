<?php

namespace Malico\MeSomb\Helper;

trait HasTransactions
{
    /**
     * Deposit|Payment Transaction
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function transaction()
    {
        return $this->morphOne('Malico\MeSomb\Model\Transaction', 'transacable');
    }

    public function toggleToSuccess()
    {
        $this->update(['success' => true]);
        
        $this->save();
    }
}
