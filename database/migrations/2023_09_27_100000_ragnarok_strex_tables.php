<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('strex_transactions', function (Blueprint $table)
        {
            $table->id('id');
            $table->date('transaction_date');
            $table->string('transaction_id');
            $table->dateTime('created');
            $table->dateTime('send_time');
            $table->string('sender', 15);
            $table->string('sender_prefix', 5)->nullable();
            $table->string('recipient', 15);
            $table->string('recipient_prefix', 5)->nullable();
            $table->string('message_content')->nullable();
            $table->smallInteger('message_parts');
            $table->string('status_code')->nullable();
            $table->string('status_code_info')->nullable();
            $table->string('keyword_id')->nullable();
            $table->string('keyword')->nullable();
            $table->string('correlation_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('smsc_transaction_id')->nullable();
            $table->string('operator')->nullable();
            $table->boolean('is_stop_message')->nullable()->default(false);
            $table->string('processed')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('business_model')->nullable();
            $table->integer('service_code')->nullable();
            $table->string('merchant_id')->nullable();
            $table->integer('result_code')->nullable();
            $table->string('result_info')->nullable();
            $table->string('invoice_text')->nullable();
            $table->smallInteger('age')->nullable();
            $table->boolean('is_restricted')->nullable()->default(false);
            $table->string('handling_company')->nullable();
            $table->string('handling_company_info')->nullable();
            $table->string('tags')->nullable();
            $table->string('channel_id')->nullable();
            $table->string('properties')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strex_transactions');
    }
};
