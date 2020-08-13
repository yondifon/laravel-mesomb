<?php

namespace Malico\MeSomb\Jobs;

use Illuminate\Bus\Queueable;
use Malico\MeSomb\Transaction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckFailedTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Transaction Model
     *
     * @var \Malico\MeSomb\Model\Payment | \Malico\MeSomb\Model\Deposit
     */
    protected $model;

    /**
     * Create a new job instance.
     *
     * @param \Malico\MeSomb\Model\Payment $model
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = Transaction::checkStatus($this->model);

        if ($transaction->successful()) {
            $this->model->toggleToSuccess();
        }
    }
}
