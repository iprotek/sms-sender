<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsClientMobileNoInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_client_mobile_no_infos', function (Blueprint $table) {
            
            $table->iprotekDefaultColumns();

            $table->string('mobile_no');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->longText('other_infos')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_client_mobile_no_infos');
    }
}
