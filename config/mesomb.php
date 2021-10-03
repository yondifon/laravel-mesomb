<?php

return [

    /*
     * Api Version
     */
    'version' => 'v1.0',

    /*
     * MeSomb Application Key
     * Copy from https://mesomb.hachther.com/en/applications/{id}
     */
    'key' => env('MeSomb_APP_KEY'),

    /*
     * MeSomb API Application Key
     * Copy from https://mesomb.hachther.com/en/applications/{id}
     */
    'api_key' => env('MeSomb_API_KEY'),

    /*
     * PIN used for MeSomb Pin
     * Configure @ https://mesomb.hachther.com/en/applications/{id}/settings/setpin/
     */
    'pin' => env('MeSomb_PIN', null),

    /*
     * Supported Payment Methods
     */
    'currencies' => ['XAF', 'XOF'],

    /*
     * Support Payment Methods
     * Array in order of preference
     */
    'services' => ['MTN', 'ORANGE'],

    /*
     * Set to True if your application uses uuid instead auto-incrmenting ids
     */
    'uses_uuid' => false,

    /*
     * Used to store the application Status
     */
    'application_cache_key' => 'mesomb_application_status',

    'throw_exceptions' => true,
];
