<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('package_id')->unsigned();
            $table->date('date');
            $table->integer('downloads')->unsigned();
            $table->enum('type', ['daily', 'weekly', 'monthly', 'yearly']);

            $table->unique(['type', 'package_id', 'date']);
            $table->index(['type', 'downloads', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('downloads');
    }
}
