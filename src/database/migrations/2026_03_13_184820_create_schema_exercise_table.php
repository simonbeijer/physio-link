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
        Schema::create('schema_exercise', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('schema_id');
            $table->unsignedBigInteger('exercise_id');
            $table->integer('order');
            $table->integer('comfort_level')->nullable();
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('schema_id')->references('id')->on('schemas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_exercise');
    }
};
