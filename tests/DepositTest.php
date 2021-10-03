<?php

use function Pest\Laravel\assertDatabaseHas;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Malico\MeSomb\Deposit;

function fakeDepositResponse()
{
    Config::set('mesomb.pin', '1234');

    Http::fake([
        'https://mesomb.hachther.com/api/*' => Http::response([
            'success'     => true,
            'redirect'    => 'https://malico.me',
            'message'     => 'Deposit Successful',
            'status'      => 'SUCCESS',
            'transaction' => [
                'id'          => Str::uuid(),
                'pk'          => Str::uuid(),
                'status'      => 'SUCCESS',
                'amount'      => '100',
                'type'        => 'DEPOSIT',
                'service'     => 'MTN',
                'message'     => 'Deposit Successful',
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

function fakeServerErrorResponse()
{
    Http::fake([
        'https://mesomb.hachther.com/api/*' => Http::response([], 500),
    ]);
}

function fakeClientErrorResponse($code = 'subscriber-insufficient-balance')
{
    Http::fake([
        'https://mesomb.hachther.com/api/*' => Http::response([
            'code'     => $code,
            'detail'   => 'Subscriber does not have enough balance',
        ], 500),
    ]);
}

it('sends deposit api request', function () {
    fakeDepositResponse();

    $request = new Deposit('676956703', 196);
    $request->pay();

    Http::assertSent(fn (Request $clientRequest) => Str::is('https://mesomb.hachther.com/api/v*/applications/*/deposit/', $clientRequest->url()));
});

it('sends required fields to deposit api request', function () {
    fakeDepositResponse();

    $request = new Deposit('676956703', 196);
    $request->pay();

    Http::assertSent(function (Request $clientRequest) {
        ray($clientRequest->data());

        return Arr::has(
            $clientRequest->data(),
            ['amount', 'service', 'receiver', 'pin']
        );
    });
});

it('creates deposit model after api_request', function () {
    fakeDepositResponse();

    $request = new Deposit('676956703', 196);

    $deposit = $request->pay();

    assertDatabaseHas('mesomb_deposits', [
        'id'      => $deposit->id,
        'success' => true,
    ]);
});

it('creates transaction model after api_request', function () {
    fakeDepositResponse();

    $request = new Deposit('676956703', 196);

    $deposit = $request->pay();

    assertDatabaseHas('mesomb_transactions', [
        'id'      => $deposit->transaction->id,
        'status'  => 'SUCCESS',
    ]);
});

it('creates failed deposit model after api_request', function () {
    fakeServerErrorResponse(null);
    withoutExceptionHandling();

    $request = new Deposit('676956703', 196);

    $deposit = $request->pay();

    assertDatabaseHas('mesomb_deposits', [
        'id'      => $deposit->id,
        'success' => false,
    ]);
});
