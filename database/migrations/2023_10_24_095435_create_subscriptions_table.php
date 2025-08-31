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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('plan_id');
            $table->decimal('price', 10, 2);
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
            $table->boolean('status')->default(1)->nullable(); //0=expired 1=active
            $table->dateTime('will_expire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
