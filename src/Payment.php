<?php

namespace Malico\MeSomb;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Malico\MeSomb\Helper\{HandleExceptions, PaymentData, RecordTransaction};
use Malico\MeSomb\Model\Payment as PaymentModel;
use Malico\MobileCM\Network;

class Payment
{
    use HandleExceptions, PaymentData, RecordTransaction;

    /**
     * MeSomb Payment Payment URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Payment Model.
     *
     * @var null|Malico\MeSomb\Model\Payment
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
        $this->message = $message;
        $this->redirect = $redirect;
    }

    /**
     * Generate Payment URL.
     */
    protected function generateURL(): void
    {
        $version = config('mesomb.version');

        $this->url = "https://mesomb.hachther.com/api/{$version}/payment/online/";
    }

    /**
     * Determine payer's Network.
     */
    protected function getPayerService(): string
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
     * Save Payment before request.
     *
     * @param array $data
     */
    protected function savePayment($data): array
    {
        $this->payment_model = PaymentModel::create($data);

        $data['reference'] = $this->reference ?? $this->payment_model->id;
        $this->request_id = $this->request_id ?? $this->payment_model->id;

        return $data;
    }

    /**
     * Prep Request Data.
     */
    protected function prepareData(): array
    {
        $data = [
            'service' => $this->service,
            'amount'  => $this->amount,
            'payer'   => $this->payer,
            'fees'    => $this->fees,
            'currency'=> $this->currency,
            'message' => $this->message,
            'redirect'=> $this->redirect,
        ];

        return array_filter($this->savePayment($data), fn ($val) => ! is_null($val));
    }

    /**
     * Send Payment Request.
     *
     * @return \Malico\MeSomb\Model\Payment
     */
    public function pay()
    {
        $data = $this->prepareData();

        $headers = [
            'X-MeSomb-Application'   => config('mesomb.key'),
            'X-MeSomb-RequestId'     => $this->request_id,
            'X-MeSomb-OperationMode' => config('mesomb.mode'),
        ];

        $response = Http::withHeaders($headers)
            ->post($this->url, $data);

        $this->recordPayment($response->json());

        if ($response->failed()) {
            $this->handleException($response);
        }

        return $this->payment_model;
    }

    /**
     * Record Response to DATABAase.
     *
     * @param array|json $response
     *
     * @return void
     */
    protected function recordPayment($response)
    {
        $data = Arr::only($response, ['status', 'success', 'message']);

        $this->payment_model->update($data);

        $this->recordTransaction($response, $this->payment_model);
    }
}
