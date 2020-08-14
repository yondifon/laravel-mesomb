<?php

namespace Malico\MeSomb;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Malico\MeSomb\Helper\PaymentData;
use Malico\MeSomb\Jobs\CheckFailedTransactions;
use Malico\MeSomb\Model\Payment as PaymentModel;
use Malico\MobileCM\Network;

class Payment
{
    use PaymentData;

    /**
     * MeSomb Payment Payment URL
     *
     * @var string
     */
    protected $url;

    /**
     * Payment Model
     *
     * @var Malico\MeSomb\Model\Payment | null
     */
    protected $payment_model;

    public function __construct(
        $payer,
        $amount,
        $service = null,
        $currency = 'XAF',
        $fees = true,
        $message = null,
        $redirect = null
    ) {
        $this->generateURL();

        $this->payer = trim($payer, '+');
        $this->amount = $amount;
        $this->service = $service ?? $this->getPayerService();
        $this->currency = $currency;
        $this->fees = $fees;
        $this->message  = $message;
        $this->redirect = $redirect;
    }

    /**
     * Generate Payment URL
     *
     * @return void
     */
    protected function generateURL() : void
    {
        $this->url = "https://mesomb.hachther.com/api/" . config('mesomb.version') . "/payment/online/";
    }

    /**
     * Determine payer's Network
     *
     * @return string
     */
    protected function getPayerService() : string
    {
        if (Network::isOrange($this->payer)) {
            return 'ORANGE';
        } elseif (Network::isMTN($this->payer)) {
            return 'MTN';
        } else {
            return config('mesomb.services')[0];
        }
    }
    
    /**
     * Save Payment before request
     *
     * @param array  $data
     *
     * @return array
     */
    protected function savePayment($data) : array
    {
        $this->payment_model = PaymentModel::create($data);

        $data["reference"] = $this->reference ?? $this->payment_model->id;
        $this->request_id = $this->request_id ?? $this->payment_model->id;

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
            "payer"=> $this->payer,
            "fees"=> $this->fees,
            "currency"=> $this->currency,
            "message"=> $this->message,
            "redirect"=> $this->redirect
        ];

        return array_filter($this->savePayment($data));
    }

    /**
     * Send Payment Request
     *
     * @return \Malico\MeSomb\Model\Payment
     */
    public function pay()
    {
        $data = $this->prepareData();

        $headers = [
            'X-MeSomb-Application' => config('mesomb.key'),
            'X-MeSomb-RequestId' => $this->request_id
        ];

        $response = Http::withToken(config('mesomb.api_key'), 'Token')
            ->withHeaders($headers)
            ->post($this->url, $data);

        if ($response->serverError()) {
            if (config('mesomb.failed_payments.check')) {
                CheckFailedTransactions::dispatch($this->payment_model);
            }
            
            $response->throw();
        }

        $this->recordPayment($response->json());

        return $this->payment_model;
    }

    /**
     * Record Payment Transaction
     *
     * @param  array $data
     *
     * @return void
     */
    protected function recordTransaction($data) : void
    {
        $data['ts'] = Carbon::parse($data['ts']);

        $this->payment_model->transaction()->updateOrCreate($data);
    }

    /**
     * Record Response to DATABAase
     *
     * @param array|json $response
     *
     * @return void
     */
    protected function recordPayment($response)
    {
        $data = Arr::only($response, ['status', 'success', 'message']);

        $this->payment_model->update($data);

        if (Arr::has($response, 'transaction')) {
            $transaction = Arr::get($response, 'transaction');
            
            $this->recordTransaction($transaction);
        }
    }
}
