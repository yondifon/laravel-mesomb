<?php

namespace Malico\MeSomb;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Malico\MeSomb\Helper\{HandleExceptions, RecordTransaction};
use Malico\MeSomb\Model\Deposit as DepositModel;
use Malico\MobileCM\Network;

class Deposit
{
    use HandleExceptions, RecordTransaction;

    /**
     * Deposit URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Reference to add in the payment.
     *
     * @var string
     */
    protected $pin;

    /**
     * Deposit Model.
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
     * Generate Deposit URL.
     */
    protected function generateURL(): void
    {
        $version = config('mesomb.version');
        $key = config('mesomb.key');

        $this->url = "https://mesomb.hachther.com/api/{$version}/applications/{$key}/deposit/";
    }

    /**
     * Determine receiver's Network.
     */
    protected function getReceiverService(): string
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
     * Save Deposit bef[return description]ore request.
     *
     * @param array $data
     */
    protected function saveDeposit($data): array
    {
        $this->deposit_model = DepositModel::create($data);

        $data['pin'] = config('mesomb.pin');

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
            'receiver'=> trim($this->receiver, '+'),
        ];

        return array_filter($this->saveDeposit($data));
    }

    /**
     * Record Deposit.
     *
     * @return void
     */
    protected function recordDeposit($response)
    {
        $data = Arr::only($response, ['status', 'success', 'message']);

        $this->deposit_model->update($data);

        $this->recordTransaction($response, $this->deposit_model);
    }

    /**
     * Make Deposit Request.
     */
    public function pay(): DepositModel
    {
        $data = $this->prepareData();

        $response = Http::withToken(config('mesomb.api_key'), 'Token')
            ->post($this->url, $data);

        $this->recordDeposit($response->json());

        if ($response->failed()) {
            $this->handleException($response);
        }

        return $this->deposit_model;
    }
}
