<?php

namespace Malico\MeSomb;

use Illuminate\Support\Facades\Http;

class Transactions
{
    /**
     * Generate transactions url URL.
     */
    public static function getURL(): string
    {
        return 'https://mesomb.hachther.com/api/'
            . config('mesomb.version')
            . '/applications/'
            . config('mesomb.key')
            . '/transactions/';
    }

    /**
     * Get all Transactions.
     *
     * @return null|\Transactions
     */
    public static function getTransactions()
    {
        $response = Http::withToken(config('mesomb.api_key'), 'Token')
            ->get(self::getURL());

        $response->throw();

        if ($response->successful()) {
            return $response->json();
        }
    }
}
