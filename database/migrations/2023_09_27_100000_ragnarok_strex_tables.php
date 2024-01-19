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
        Schema::create('strex_transactions', function (Blueprint $table) {
            $table->id('id')->comment('Unique numeric ID in Ragnarok');
            $table->date('transaction_date')->comment('Date only, no timestamp');
            $table->string('transaction_id')->comment('UUID');
            $table->dateTime('created')->comment('Full timestamp. Date and time');
            $table->dateTime('send_time')->comment('Full timestamp. Date and time');
            $table->string('sender', 15)->comment('Phone number of sms service? Always 2002');
            $table->string('sender_prefix', 5)->nullable();
            $table->string('recipient', 15)->comment('Phone number of receipient.');
            $table->string('recipient_prefix', 5)->nullable()->comment('Country code of phone number');
            $table->string('message_content')->nullable()->comment('Actual SMS sent to recipient');
            $table->smallInteger('message_parts')->comment('Number of message parts in sms?');
            $table->string('status_code')->nullable()->comment('Common values: `Ok`, `Failed`, `Queued`, `Reversed` and `Sent`');
            $table->string('status_code_info')->nullable()->comment('More detailed code/description of status, usually when failed');
            $table->string('keyword_id')->nullable()->comment('ID of sms keyword in UUID format');
            $table->string('keyword')->nullable()->comment('Keyword used in initial sms from recipient. `BUSS`, `BARN`, `HONNØR`, etc.');
            $table->string('correlation_id')->nullable()->comment('Strex internal ID of sort. UUID.');
            $table->string('session_id')->nullable()->comment('Strex internal ID of sort. UUID.');
            $table->string('smsc_transaction_id')->nullable()->comment(`Usually recipient phone number. 34 digit HEX code occurs occationally`);
            $table->string('operator')->nullable()->comment('Phone operator of recipient. Example values: `no.telia`, `no.telenor`');
            $table->boolean('is_stop_message')->nullable()->default(false)->comment('Always 0. Probably strex internal value for wheter to terminate subscription services');
            $table->string('processed')->nullable()->comment('Always null?');
            $table->decimal('price', 10, 2)->nullable()->comment('Price of sms service. Relates to keyword. Is null when `smsc_transaction_id is hex digit`');
            $table->string('business_model')->nullable()->comment('Unknown. Null or `STREX-PAYMENT-EXTENDED-LIMIT`');
            $table->integer('service_code')->nullable()->comment('Always `6002`. Strex internal code');
            $table->string('merchant_id')->nullable()->comment('County code, strex style. Usually `mer_troms_og_fin` or `mer_troms_og_fin_02`');
            $table->integer('result_code')->nullable()->comment('`0` on success, it seems.');
            $table->string('result_info')->nullable()->comment('Usually `Success` or null. Has more detailed descriptions when `status_code` is `Failed`');
            $table->string('invoice_text')->nullable()->comment('Probably the text appearing on customers invoice. `Troms fylkestrafikk` or null');
            $table->smallInteger('age')->nullable()->comment('Unknown. Always `0`?');
            $table->boolean('is_restricted')->nullable()->default(false)->comment('Unknown. Always `0`?');
            $table->string('handling_company')->nullable()->comment('Empty, not used');
            $table->string('handling_company_info')->nullable()->comment('Empty, not used');
            $table->string('tags')->nullable()->comment('Empty serialized array');
            $table->string('channel_id')->nullable()->comment('Empty, not used');
            $table->string('properties')->nullable()->comment('Serialized json with additional values. Strex internal stuff');
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
