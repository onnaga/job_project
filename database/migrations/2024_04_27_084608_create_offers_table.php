<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('the job');
            $table->bigInteger('specialization_wanted')->unsigned()->nullable();
            $table->foreign('specialization_wanted')->references('id')->on('specializations')->onDelete('set null');
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->integer('salary')->nullable();
            $table->string('the days')->nullable();
            $table->string('hour begin')->nullable();
            $table->string('period')->nullable();
            $table->boolean('official holidays')->nullable();
            $table->string('offer end at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
