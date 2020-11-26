<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdTicketNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification', function ($table) {
            $table->unsignedBigInteger('id_ticket')->after('id_user');

            $table->foreign('id_ticket')->references('id_ticket')->on('ticket')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification', function ($table) {
            $table->dropColumn('id_ticket');
        });
    }
}
