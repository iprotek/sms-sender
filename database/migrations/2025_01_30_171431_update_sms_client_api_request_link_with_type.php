<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSmsClientApiRequestLinkWithType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sms_client_api_request_links', function (Blueprint $table) {
            $table->string('type')->default('iprotek');
            $table->boolean('is_auto')->default(0); //
            $table->text('header_info')->nullable(); //
            $table->boolean('is_default')->default(0); //
            $table->string('api_version')->nullable();
            $table->integer('messenger_sms_api_request_link_id')->nullable();
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
