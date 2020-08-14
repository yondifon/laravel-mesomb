<?php

namespace Malico\MeSomb;

use Illuminate\Support\Arr;
use Malico\MobileCM\Network;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Malico\MeSomb\Jobs\CheckFailedTransactions;
use Malico\MeSomb\Model\Deposit as DepositModel;

class Deposit
{
    /**
     * Deposit URL
     *
     * @var string
     */
    protected $url;

    /**
     * Reference to add in the payment
     *
     * @var string
     */
    protected $pin;

    /**
     * Deposit Model
     *
     * @var Malico\MeSomb\Deposit
     */
    protected $deposit_model;

    public function __construct($receiver, $amount, $service = null)
    {
        $this->generateURL();

        $this->receiver = $receiver;
        $this->amount = $amount;
        $this->service = $service ?? $this->getReceiverService();
    }

    /**
     * Generate Deposit URL
     *
     * @return void
     */
    protected function generateURL() : void
    {
        $this->url = "https://mesomb.hachther.com/api/" .
                config('mesomb.version') .
                "/applications/" .
                config('mesomb.key') .
                "/deposit";
    }

    /**
     * Determine receiver's Network
     *
     *  @return string
     */
    protected function getReceiverService() : string
    {
        if (Network::isOrange($this->receiver)) {
            return 'ORANGE';
        } elseif (Network::isMTN($this->receiver)) {
            return 'MTN';
        } else {
            return config('mesomb.services')[0];
        }
    }

    /**
     * Save Deposit bef[return description]ore request
     *
     * @param array  $data
     *
     * @return array
     */
    protected function saveDeposit($data) : array
    {
        $this->deposit_model = DepositModel::create($data);

        $data ["pin"] = config('mesomb.pin');

        return $data;
    }

    /**
     * Prep Request Data
     *
     * @return array
     */
    protected function prepareData() : array
    {
        $data =  [
            "service"=> $this->service,
            "amount"=> $this->amount,
            "receiver"=> trim($this->receiver, '+')
        ];

        return array_filter($this->saveDeposit($data));
    }

    /**
     * Record Response to DATABAase
     *
     * @param array|json $response
     *
     * @return void
     */
    protected function recordTransaction($data) : void
    {
        $data['ts'] = Carbon::parse($data['ts']);

        $this->deposit_model->transaction()->updateOrCreate($data);
    }

    /**
     * Record Deposit
     *
     * @return void
     */
    protected function recordDeposit($response)
    {
        $data = Arr::only($response, ['status', 'success', 'message']);

        $this->deposit_model->update($data);

        if (Arr::has($response, 'transaction')) {
            $transaction = Arr::get($response, 'transaction');
            
            $this->recordTransaction($transaction);
        }
    }

    /**
     * Make Deposit Request
     *
     * @return \Malico\MeSomb\Model\Deposit
     */
    public function pay() : DepositModel
    {
        $data = $this->prepareData();
        
        $response = Http::withToken(config('mesomb.api_key'), 'Token')
                    ->post($this->url, $data);
            
        if ($response->serverError()) {
            if (config('mesomb.failed_payments.check')) {
                CheckFailedTransactions::dispatchNow($this->deposit_model);
            }
        }
        
        $response->throw();

        $this->recordDeposit($response->json());

        return $this->deposit_model;
    }
}
