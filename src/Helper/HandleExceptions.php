<?php

namespace Malico\MeSomb\Helper;

use Illuminate\Http\Client\Response;
use Malico\MeSomb\Exceptions\InsufficientBalanceException;
use Malico\MeSomb\Exceptions\InvalidAmountException;
use Malico\MeSomb\Exceptions\InvalidPhoneNumberException;
use Malico\MeSomb\Exceptions\InvalidPinException;
use Malico\MeSomb\Exceptions\TimeoutException;

trait HandleExceptions
{
    public $errorCodes = [
        'subscriber-insufficient-balance' => InsufficientBalanceException::class,
        'subscriber-not-found' => InvalidPhoneNumberException::class,
        'subscriber-invalid-length' => InvalidPhoneNumberException::class,
        'subscriber-invalid-secret-code' => InvalidPinException::class,
        'subscriber-invalid-min-amount' => InvalidAmountException::class,
        'subscriber-invalid-max-amount' => InvalidAmountException::class,
        'subscriber-timeout' => TimeoutException::class,
        'subscriber-internal-error' => TimeoutException::class,
    ];

    public function handleException(Response $response)
    {
        if (! config('mesomb.throw_exceptions')) {
            return;
        }

        $body = (object) $response->json();

        if (isset($this->errorCodes[$body->code])) {
            $class = $this->errorCodes[$body->code];
            throw new $class($body->detail);
        } else {
            $response->throw();
        }
    }
}
