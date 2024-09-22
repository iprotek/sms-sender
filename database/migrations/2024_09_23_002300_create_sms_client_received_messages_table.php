<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsClientReceivedMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_client_received_messages', function (Blueprint $table) {
            $table->id();
            $table->string('from_number');
            $table->text('message'); 
            $table->integer('sms_client_api_request_link_id')->nullable();
            $table->integer('sms_api_request_link_id')->nullable();
            $table->integer('sender_id')->nullable();
            $table->integer('data_id')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_client_received_messages');
    }
}
