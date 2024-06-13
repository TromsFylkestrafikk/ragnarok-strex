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
            $table->text('message_content')->nullable()->comment('Actual SMS sent to recipient')->change();
        });
    }
};
