<?php

namespace Malico\MeSomb;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Transaction
{
    /**
     * Generate Checking URL
     *
     * @return string
     */
    public static function getURL() : string
    {
        return "https://mesomb.hachther.com/api/" . config('mesomb.version') . "/payment/online/";
    }
    
    /**
     * Check Transaction sTatus
     *
     * @param \Malico\MeSomb\Model\Payment | \Malico\MeSomb\Model\Deposit $model
     *
     * @return \Malico\MeSomb\Model\Transaction|Null
     */
    public static function checkStatus($model)
    {
        $headers = [
            'X-MeSomb-Application' => config('mesomb.key'),
            'X-MeSomb-RequestId' => $model->id
        ];

        $data = [
            'reference' => $model->id
        ];

        $response = Http::withToken(config('mesomb.api_key'))
            ->withHeaders($headers)
            ->get(self::getURL(), $data);

        $response->throw();
            
        if ($response->successful()) {
            $data = json_decode($response, true);

            $data['ts'] = Carbon::parse($data['ts']);

            $model->transaction()->updateOrCreate($data);

            return $model->transaction;
        }

        return null;
    }
}
