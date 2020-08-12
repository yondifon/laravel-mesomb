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
     * Payments
     */
    'payable' => [
        /**
         * Set this true if you're using uuid insteads of auto-increments  for id
         *
         * @var bool
         */
        'uuid' => false
    ],

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
    ]
];
