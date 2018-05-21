<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('url');
            $table->string('repository');
            $table->integer('downloads')->unsigned();
            $table->integer('favers')->unsigned();
            $table->integer('dependents')->unsigned()->nullable();
            $table->integer('github_stars')->unsigned()->nullable();
            $table->integer('github_watchers')->unsigned()->nullable();
            $table->integer('github_forks')->unsigned()->nullable();
            $table->integer('github_open_issues')->unsigned()->nullable();
            $table->string('latest_version')->nullable();
            $table->string('min_php_version')->nullable();
            $table->string('min_laravel_version')->nullable();
            $table->timestamps();

            $table->unique('name');
            $table->index(['downloads', 'favers']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
