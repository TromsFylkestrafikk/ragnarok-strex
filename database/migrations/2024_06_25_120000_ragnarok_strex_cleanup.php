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
        Schema::table('strex_transactions', function (Blueprint $table) {
            $table->dropColumn('keyword_id');
            $table->dropColumn('correlation_id');
            $table->dropColumn('session_id');
            $table->dropColumn('is_stop_message');
            $table->dropColumn('processed');
            $table->dropColumn('business_model');
            $table->dropColumn('service_code');
            $table->dropColumn('merchant_id');
            $table->dropColumn('age');
            $table->dropColumn('is_restricted');
            $table->dropColumn('handling_company');
            $table->dropColumn('handling_company_info');
            $table->dropColumn('tags');
            $table->dropColumn('channel_id');
            $table->dropColumn('properties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('strex_transactions', function (Blueprint $table) {
            $table->string('keyword_id')->nullable()->comment('ID of sms keyword in UUID format');
            $table->string('correlation_id')->nullable()->comment('Strex internal ID of sort. UUID.');
            $table->string('session_id')->nullable()->comment('Strex internal ID of sort. UUID.');
            $table->boolean('is_stop_message')->nullable()->default(false)->comment('Always 0. Probably strex internal value for wheter to terminate subscription services');
            $table->string('processed')->nullable()->comment('Always null?');
            $table->string('business_model')->nullable()->comment('Unknown. Null or `STREX-PAYMENT-EXTENDED-LIMIT`');
            $table->integer('service_code')->nullable()->comment('Always `6002`. Strex internal code');
            $table->string('merchant_id')->nullable()->comment('County code, strex style. Usually `mer_troms_og_fin` or `mer_troms_og_fin_02`');
            $table->smallInteger('age')->nullable()->comment('Unknown. Always `0`?');
            $table->boolean('is_restricted')->nullable()->default(false)->comment('Unknown. Always `0`?');
            $table->string('handling_company')->nullable()->comment('Empty, not used');
            $table->string('handling_company_info')->nullable()->comment('Empty, not used');
            $table->string('tags')->nullable()->comment('Empty serialized array');
            $table->string('channel_id')->nullable()->comment('Empty, not used');
            $table->string('properties')->nullable()->comment('Serialized json with additional values. Strex internal stuff');
        });
    }
};
