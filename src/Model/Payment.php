<?php

namespace Malico\MeSomb\Model;

use Illuminate\Database\Eloquent\Model;
use Malico\MeSomb\Helper\HasDeposits;
use Malico\MeSomb\Helper\HasTransactions;
use Malico\MeSomb\Helper\ModelUUID;

class Payment extends Model
{
    use ModelUUID, HasTransactions, HasDeposits;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Payment Model Table
     *
     * @var string
     */
    protected $table = 'mesomb_payments';

    /**
     * Guarded Properties
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * Payable Morph
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Refund Payment
     *
     * @return null|\Malico\MeSomb\Model\Deposit
     */
    public function refund()
    {
        if ($this->success &&  $this->transaction->successful()) {
            return $this->deposit($this->payer, $this->transaction->amount)->pay();
        }

        return null;
    }
}
