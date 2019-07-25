<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csvs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('excluded_word_1')->nullable();
            $table->string('excluded_word_2')->nullable();
            $table->string('excluded_word_3')->nullable();
            $table->string('excluded_word_4')->nullable();
            $table->string('excluded_word_5')->nullable();
            $table->string('excluded_word_6')->nullable();
            $table->string('excluded_word_7')->nullable();
            $table->string('excluded_word_8')->nullable();
            $table->string('excluded_word_9')->nullable();
            $table->string('excluded_word_10')->nullable();
            $table->boolean('merukari');
            $table->boolean('furiru');
            $table->boolean('rakuma');
            $table->boolean('otamato');
            $table->boolean('zozo');
            $table->boolean('ticket_camp');
            $table->boolean('shopiz');
            $table->boolean('yahoo');
            $table->boolean('bukuma');
            $table->boolean('monokyun');
            $table->integer('lower_price')->nullable();
            $table->integer('max_price')->nullable();
            $table->string('alert_name')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csvs');
    }
}
