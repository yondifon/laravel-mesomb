<?php

namespace Malico\MeSomb;

use Illuminate\Support\Facades\{Cache, Http};

class Application
{
    /**
     * Generate Deposit URL.
     *
     * @return void
     */
    protected static function generateURL(): string
    {
        return 'https://mesomb.hachther.com/api/'
                    . config('mesomb.version')
                    . '/applications/'
                    . config('mesomb.key')
                    . '/status';
    }

    /**
     * Get Cached Application Status | if null request fresh copy of Application Status.
     *
     * @return array|json
     */
    public static function status()
    {
        if (Cache::has(config('mesomb.application_cache_key'))) {
            return Cache::get(config('mesomb.application_cache_key'));
        } else {
            return self::checkStatus();
        }
    }

    /**
     * Fetch Application Status.
     *
     * @return array|json
     */
    public static function checkStatus()
    {
        $token = config('mesomb.api_key');

        // dd($headers);
        $response = Http::withToken($token, 'Token')
            ->get(self::generateURL());

        $response->throw();

        Cache::put(config('mesomb.application_cache_key'), $response->json());

        return $response->json();
    }
}
