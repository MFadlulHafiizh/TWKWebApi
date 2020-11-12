<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->id('id_ticket');
            $table->unsignedBigInteger('id_apps');
            $table->enum('type', ['Report', 'Request']);
            $table->enum('priority', ['Low', 'Middle', 'High']);
            $table->string('subject', 60);
            $table->longText('detail');
            $table->bigInteger('price')->nullable();
            $table->date('time_periodic')->nullable();
            $table->string('status', 50);
            $table->string('aproval_stat', 20)->nullable();
            $table->timestamps();

            $table->foreign('id_apps')->references('id_apps')->on('application')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket');
    }
}
