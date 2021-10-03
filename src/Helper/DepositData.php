<?php

namespace Malico\MeSomb\Helper;

trait DepositData
{
    /**
     * Amount to be Paid for Service.
     *
     * @var int|string
     */
    protected $amount;

    /**
     * Users Telephone Number.
     *
     * @var int|string
     */
    protected $receiver;

    /**
     * Payment Service
     * MTN | ORANGE.
     *
     * @var string
     */
    protected $service;

    /**
     * Modify receiver.
     *
     * @param int|string $value
     *
     * @return Malico\MeSomb\Payment
     */
    public function receiver($value)
    {
        $this->receiver = trim($value, '+');

        return $this;
    }

    /**
     * Same as receiver.
     *
     * @param string $value
     *
     * @return void
     */
    public function phone($value)
    {
        return $this->receiver($value);
    }

    /**
     * Modify Amount.
     *
     * @param int|string $value
     *
     * @return Malico\MeSomb\Payment
     */
    public function amount($value)
    {
        $this->amount = $value;

        return $this;
    }

    /**
     * Modify Service.
     *
     * @param string $value
     *
     * @return Malico\MeSomb\Payment
     */
    public function service($value)
    {
        $this->service = $value;

        return $this;
    }
}
