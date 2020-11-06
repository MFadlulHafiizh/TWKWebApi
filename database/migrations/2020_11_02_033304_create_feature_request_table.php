<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_request', function (Blueprint $table) {
            $table->id('id_request');
            $table->unsignedBigInteger('id_apps');
            $table->enum('priority', ['Low', 'Middle', 'High']);
            $table->string('subject', 60);
            $table->mediumText('detail');
            $table->bigInteger('price')->nullable()->default(20);
            $table->date('time_periodic')->nullable();
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
        Schema::dropIfExists('feature_request');
    }
}
