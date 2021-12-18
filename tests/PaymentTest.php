<?php

namespace Tests\Unit;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\{Config, Http};
use Illuminate\Support\{Arr, Str};
use Malico\MeSomb\Exceptions\{InsufficientBalanceException, InvalidPhoneNumberException, InvalidPinException};
use Malico\MeSomb\Payment;
use function Pest\Laravel\assertDatabaseHas;

function fakePaymentResponse()
{
    Config::set('mesomb.pin', '1234');

    Http::fake([
        'https://mesomb.hachther.com/api/*' => Http::response([
            'success'     => true,
            'redirect'    => 'https://malico.me',
            'message'     => 'Payment Successful',
            'status'      => 'SUCCESS',
            'transaction' => [
                'id'          => Str::uuid(),
                'pk'          => Str::uuid(),
                'status'      => 'SUCCESS',
                'amount'      => '100',
                'type'        => 'PAYMENT',
                'service'     => 'MTN',
                'message'     => 'PAYMENT    Successful',
                'b_party'     => '+237676956703',
                'fees'        => 3,
                'external_id' => '',
                'ts'          => '2020-01-01T00:00:00.000Z',
                'reference'   => '',
                'direction'   => 1,
            ],
        ], 200),
    ]);
}

function fakeServerErrorResponse($code = 'subscriber-insufficient-balance', $message = 'Subscriber does not have enough balance')
{
    Http::fake([
        'https://mesomb.hachther.com/api/*' => Http::response([
            'code'     => $code,
            'detail'   => $message,
        ], 500),
    ]);
}

function fakeClientErrorResponse($statusCode = 400)
{
    Http::fake([
        'https://mesomb.hachther.com/api/*' => Http::response([], $statusCode),
    ]);
}

it('makes payment to right api url', function () {
    fakePaymentResponse();

    $request = new Payment(67282929, 100);
    $request->pay();

    Http::assertSent(fn (Request $clientRequest) => Str::is('https://mesomb.hachther.com/api/v*/payment/online/', $clientRequest->url()));
});

it('sends required information', function () {
    fakePaymentResponse();

    $request = new Payment(67282929, 100);
    $request->pay();

    Http::assertSent(function (Request $clientRequest) {
        ray($clientRequest->data());

        return Arr::has(
            $clientRequest->data(),
            ['amount', 'service', 'payer']
        );
    });
});

it('creates payment model after request', function () {
    fakePaymentResponse();

    $request = new Payment(67282929, 100);
    $payment = $request->pay();

    assertDatabaseHas('mesomb_payments', [
        'id'      => $payment->id,
        'amount'  => 100,
        'status'  => 'SUCCESS',
        'service' => 'MTN',
        'payer'   => 67282929,
    ]);
});

it('creates transaction model after api_request', function () {
    fakePaymentResponse();

    $request = new Payment(67282929, 100);
    $payment = $request->pay();

    assertDatabaseHas('mesomb_transactions', [
        'id'          => $payment->transaction->id,
        'pk'          => $payment->transaction->pk,
        'status'      => 'SUCCESS',
        'amount'      => 100,
        'type'        => 'PAYMENT',
    ]);
});

it('creates failed payment model after api_request', function () {
    fakeServerErrorResponse();
    withoutExceptionHandling();

    $request = new Payment(67282929, 100);
    $payment = $request->pay();

    assertDatabaseHas('mesomb_payments', [
        'id'      => $payment->id,
        'success' => false,
    ]);
});

it('throws exception when balance is insufficient', function () {
    fakeServerErrorResponse();

    $request = new Payment(67282929, 100);

    expect(fn () => $request->pay())->toThrow(InsufficientBalanceException::class, 'Subscriber does not have enough balance');
});

it('throws exception when payer is not a subscriber', function () {
    fakeServerErrorResponse('subscriber-not-found');

    $request = new Payment(67282929, 100);

    expect(fn () => $request->pay())->toThrow(InvalidPhoneNumberException::class);
});

it('throws exception when payer length is too long', function () {
    fakeServerErrorResponse('subscriber-invalid-length');

    $request = new Payment(67282929, 100);

    expect(fn () => $request->pay())->toThrow(InvalidPhoneNumberException::class);
});

it('throws exception when pin is invalid', function () {
    fakeServerErrorResponse('subscriber-invalid-secret-code');

    $request = new Payment(67282929, 100);

    expect(fn () => $request->pay())->toThrow(InvalidPinException::class);
});
