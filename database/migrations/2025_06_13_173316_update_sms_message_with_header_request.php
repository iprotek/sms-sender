<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSmsMessageWithHeaderRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sms_client_messages', function (Blueprint $table) {
            //$table->iprotekDefaultColumns();
            $table->bigInteger('client_id')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
