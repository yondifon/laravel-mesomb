<?php

return [

    /**
     * Api Version
     *
     * @var string
     */
    'version' => 'v1.0',

    /**
     * MeSomb Application Key
     * Copy from https://mesomb.hachther.com/en/applications/{id}
     *
     * @var string
     */
    'key' => env('MeSomb_APP_KEY'),

    /**
     * MeSomb API Application Key
     * Copy from https://mesomb.hachther.com/en/applications/{id}
     *
     * @var string
     */
    'api_key' => env('MeSomb_API_KEY'),

    /**
     * PIN used for MeSomb Pin
     * Configure @ https://mesomb.hachther.com/en/applications/{id}/settings/setpin/
     *
     * @var int|string
     */
    'pin' => env('MeSomb_PIN', null),

    /**
     * Supported Payment Methods
     *
     * @var array
     */
    'currencies' => ['XAF', 'XOF'],

    /**
     * Support Payment Methods
     * Array in order of preference
     *
     * @var array
     */
    'services' => ['MTN', 'ORANGE'],

    /**
     * Set to True if your application uses uuid instead auto-incrmenting ids
     *
     * @var bool
     */
    'uses_uuid' => false,


    /**
     * Failed Payments
     *
     * @var array
     */
    'failed_payments' => [
        /**
         * Add Failed requests to queue ( to check transactions)
         *
         * @var bool
         */
        'check' => false
    ],

    /**
     * Application Cache Key
     *
     * Used to store the application Status
     */
    'application_cache_key' => 'mesomb_application_status'
];
