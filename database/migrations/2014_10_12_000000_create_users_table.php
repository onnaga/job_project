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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('specialization_id')->unsigned()->nullable();
            $table->foreign('specialization_id')->references('id')->on('specializations')->onDelete('set null');
            $table->bigInteger('level_id')->unsigned()->nullable();
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('set null');
            $table->bigInteger('photo_id')->unsigned()->nullable();
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->string('age')->nullable();
            $table->rememberToken();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
