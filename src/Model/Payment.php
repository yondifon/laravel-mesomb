<?php

namespace Malico\MeSomb\Model;

use Illuminate\Database\Eloquent\Model;
use Malico\MeSomb\Helper\HasTransactions;
use Malico\MeSomb\Helper\ModelUUID;

class Payment extends Model
{
    use ModelUUID, HasTransactions;

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
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function payable()
    {
        return $this->morphTo();
    }
}
