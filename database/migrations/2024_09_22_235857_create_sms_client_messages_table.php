<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsClientMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_client_messages', function (Blueprint $table) {
            $table->id();
            $table->string('to_number');
            $table->text('message');
            $table->boolean('status_id')->nullable();
            $table->string('status_info')->nullable();
            $table->dateTime('status_at')->nullable();
            $table->integer('sms_client_api_request_link_id')->nullable();
            $table->integer('sms_api_request_link_id')->nullable();
            $table->integer('data_id')->nullable();
            $table->integer('sender_id')->nullable();
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
        Schema::dropIfExists('sms_client_messages');
    }
}
