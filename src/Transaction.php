<?php

namespace Malico\MeSomb;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Malico\Momo\Model\Transaction;

class Transaction
{
    /**
     * Generate Checking URL.
     *
     * @return string
     */
    public static function getURL(string $id) : string
    {
        return 'https://mesomb.hachther.com/api/' .
            config('mesomb.version') .
            '/applications/' .
            config('mesomb.key') .
            '/transactions/' .
            $id;
    }

    /**
     * Check Transaction sTatus.
     *
     * @param \Malico\MeSomb\Model\Payment | \Malico\MeSomb\Model\Deposit $model
     *
     * @return \Malico\MeSomb\Model\Transaction|null
     */
    public static function checkStatus($model)
    {
        if (is_string($model)) {
            $id = $model;
        } else {
            $id = $model->transaction->pk;
        }

        $response = Http::withToken(config('mesomb.api_key'), 'Token')
            ->get(self::getURL($id));

        $response->throw();

        if ($response->successful()) {
            $data = $response->json();

            $data['ts'] = Carbon::parse($data['ts']);

            if (! is_string($model)) {
                $model->transaction()->updateOrCreate($data);

                return $model->transaction;
            } else {
                return self::updateOrCreate($data);
            }
        }

    }
}
