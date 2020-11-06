<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportBugTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_bug', function (Blueprint $table) {
            $table->id('id_report');
            $table->unsignedBigInteger('id_apps');
            $table->enum('priority', ['Low', 'Middle', 'High']);
            $table->string('subject', 60);
            $table->mediumText('detail');
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
        Schema::dropIfExists('report_bug');
    }
}
