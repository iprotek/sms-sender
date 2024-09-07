<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTicketMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->integer('sms_ticket_id');
            $table->text('message');
            $table->integer('chat_by')->default(0); //IF 0, mean support / customer
            $table->string('chat_by_email')->nullable(); 
            $table->string('chat_by_name'); // name of support / customer

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_ticket_messages');
    }
}
