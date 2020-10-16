<?php

namespace Malico\MeSomb\Helper;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

trait RecordTransaction
{
    protected $transaction_fields = [
        'pk',
        'status',
        'amount',
        'type',
        'service',
        'message',
        'b_party',
        'fees',
        'external_id',
        'ts',
        'direction',
        'reference'
    ];
    
    /**
     * Extract on Fields saved in DB
     *
     * @param array $data
     *
     * @return array
     */
    protected function extractSavableTransactionDetails(array $data) : array
    {
        return Arr::only($data, $this->transaction_fields);
    }

    /**
     * Save {Model} Transaction
     *
     * @param array $data
     *
     * @return void
     */
    protected function saveTransaction($data, $model) : void
    {
        $data = $this->extractSavableTransactionDetails($data);

        $data['ts'] = Carbon::parse($data['ts']);
        $data['direction'] = "".$data['direction']."";

        $model->transaction()->updateOrCreate($data);
    }

    /**
     * Save Transaction
     *
     * @param   array  $response
     * @param   \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function recordTransaction(array $response, $model) : void
    {
        if (Arr::has($response, 'transaction')) {
            $transaction = Arr::get($response, 'transaction');

            $this->saveTransaction($transaction, $model);
        }
    }
}
