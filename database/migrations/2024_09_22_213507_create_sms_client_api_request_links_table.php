<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsClientApiRequestLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_client_api_request_links', function (Blueprint $table) {
            $table->id();
            
            //
            $table->bigInteger('group_id')->nullable();
            $table->bigInteger('pay_created_by')->nullable(); 
            $table->bigInteger('pay_updated_by')->nullable();
            $table->bigInteger('pay_deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->string('name');
            $table->string('api_name');
            $table->string('api_username');
            $table->string('api_password');
            $table->longText('api_url');
            $table->boolean('is_active')->default(1);
            $table->string('inactive_reason')->nullable();
            $table->dateTime('inactive_at')->nullable();
            $table->longText('webhook_response_url')->nullable();
            $table->boolean('is_webhook_active')->default(1);
            $table->integer('priority')->nullable();
            $table->dateTime('last_sending_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_client_api_request_links');
    }
}
