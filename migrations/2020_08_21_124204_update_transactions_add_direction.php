<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsAddDirection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'mesomb_transactions',
            function (Blueprint $table) {
                $table->enum('direction', ['0', '-1', '1'])->nullable();
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
        Schema::table(
            'mesomb_transactions',
            function (Blueprint $table) {
                $table->dropColumn('direction');
            }
        );
    }
}
