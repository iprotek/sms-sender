<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FnGetUnSeenSmsCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        
        DB::statement("SET @@sql_mode='';");
        DB::unprepared("DROP FUNCTION IF EXISTS fnGetUnSeenSMSCount");
        DB::unprepared("
        CREATE FUNCTION `fnGetUnSeenSMSCount`(_from_mobile_no VARCHAR(20)) RETURNS int
        BEGIN
            DECLARE _Count INT;
            SELECT COUNT(id) INTO _Count FROM sms_client_received_messages WHERE from_number = _from_mobile_no AND seen_at IS NULL;
        RETURN _Count;
        END
        ");
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
