<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('transaction_type_id');
            $table->mediumInteger('user_id');
            $table->Integer('customer_id')->nullable();
            $table->string('cheque_number')->nullable();
            $table->unsignedDecimal('Amount',12,2);
            $table->timestamp('due_date')->nullable();
            $table->boolean('isEdited')->default(0);
            $table->timestamps();
            $table->index(['customer_id']);
        });

        Schema::create('transaction_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type_name');
            $table->string('abbreviation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('transaction_types');
    }
}
