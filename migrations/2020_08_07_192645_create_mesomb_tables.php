<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesombTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'mesomb_payments',
            function (Blueprint $table) {
                $table->uuid('id')->primary();

                // Pre Requent Data
                $table->enum('service', config('mesomb.services'));
                $table->boolean('fees')->default(true);
                $table->string('payer');
                $table->float('amount');
                $table->text('message')->nullable();
                $table->enum(
                    'currency',
                    config('mesomb.currencies')
                )->default(
                    config('mesomb.currencies')[0]
                );
                $table->string('redirect')->nullable();

                // Post Request
                $table->boolean('success')->default(false);
                $table->enum('status', ['FAIL', 'SUCCESS'])->nullable();

                if (config('mesomb.uses_uuid')) {
                    $table->nullableUuidMorphs('payable');
                } else {
                    $table->nullableMorphs('payable');
                }

                $table->timestamps();
            }
        );

        Schema::create(
            'mesomb_deposits',
            function (Blueprint $table) {
                $table->uuid('id')->primary();

                // Pre Requent Data
                $table->enum('service', config('mesomb.services'));
                $table->string('receiver');
                $table->float('amount');

                // Post Request
                $table->text('message')->nullable();
                $table->string('redirect')->nullable();
                $table->boolean('success')->default(false);
                $table->enum('status', ['FAIL', 'SUCCESS'])->nullable();

                if (config('mesomb.uses_uuid')) {
                    $table->nullableUuidMorphs('depositable');
                } else {
                    $table->nullableMorphs('depositable');
                }

                $table->timestamps();
            }
        );

        Schema::create(
            'mesomb_transactions',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('pk');
                $table->string('status');
                $table->float('amount');
                $table->enum(
                    'type',
                    [
                        'COLLECT',
                        'REFILL',
                        'INIT',
                        'WITHDRAWAL',
                        'PAYMENT',
                        'DEPOSIT',
                        'TRANSFER',
                        'SendMoney',
                        'ReceiveMoney',
                        'P2PTransfer',
                        'AccountBalance',
                        'CashIn', 'CashOut',
                        'ENEOBill',
                        'FloatTransfer',
                        'SellAirtime',
                        'PayWithCoupon',
                        'PostpaidBill',
                        'AirtimePurchase',
                        'ENEOPrepaid',
                        'CDEBill',
                    ]
                );
                $table->string('service');
                $table->text('message')->nullable();
                $table->text('b_party')->nullable();
                $table->float('fees');
                $table->string('external_id')->nullable();
                $table->timestamp('ts');
                $table->uuidMorphs('transacable');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesomb_transactions');
        Schema::dropIfExists('mesomb_deposits');
        Schema::dropIfExists('mesomb_payments');
    }
}
