<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_tickets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();


            $table->string('title');
            $table->longText('details');
            $table->integer('created_by'); //0-means from customer
            $table->integer('updated_by')->nullable();
            $table->string('app_url');
            $table->string('app_name');
            $table->string('ticket_type')->default('system-support'); // customer, system-support
            $table->string('category_name')->nullable();

            //CUSTOMER
            $table->string('customer_account_no')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_contact_no')->nullable();

            //CATERER
            $table->integer('cater_by_id')->default(0);
            $table->string('cater_by_name')->nullable();
            $table->dateTime('cater_at')->nullable();

            //
            $table->integer('current_status_id')->default(0);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_tickets');
    }
}
