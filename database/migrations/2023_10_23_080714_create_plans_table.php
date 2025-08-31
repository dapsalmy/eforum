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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('duration');
            $table->boolean('verified')->default(0)->nullable();
            $table->integer('points');
            $table->boolean('categories')->default(0)->nullable();
            $table->boolean('tips')->default(0)->nullable();
            $table->boolean('comments')->default(0)->nullable();
            $table->boolean('reactions')->default(0)->nullable();
            $table->boolean('followers')->default(0)->nullable();
            $table->boolean('messages')->default(0)->nullable();
            $table->boolean('users')->default(0)->nullable();
            $table->boolean('viewers')->default(0)->nullable();
            $table->boolean('ads')->default(0)->nullable();
            $table->integer('order');
            $table->boolean('status')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
