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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('categories')->default(0)->nullable();
            $table->boolean('badges')->default(0)->nullable();
            $table->boolean('posts')->default(0)->nullable();
            $table->boolean('comments')->default(0)->nullable();
            $table->boolean('replies')->default(0)->nullable();
            $table->boolean('chats')->default(0)->nullable();
            $table->boolean('reports')->default(0)->nullable();
            $table->boolean('ban_durations')->default(0)->nullable();
            $table->boolean('banned_users')->default(0)->nullable();
            $table->boolean('plans')->default(0)->nullable();
            $table->boolean('buy_points')->default(0)->nullable();
            $table->boolean('deposits')->default(0)->nullable();
            $table->boolean('subscriptions')->default(0)->nullable();
            $table->boolean('tips')->default(0)->nullable();
            $table->boolean('withdrawals')->default(0)->nullable();
            $table->boolean('transactions')->default(0)->nullable();
            $table->boolean('users')->default(0)->nullable();
            $table->boolean('roles')->default(0)->nullable();
            $table->boolean('pages')->default(0)->nullable();
            $table->boolean('faqs')->default(0)->nullable();
            $table->boolean('site_settings')->default(0)->nullable();
            $table->boolean('gateways')->default(0)->nullable();
            $table->boolean('language')->default(0)->nullable();
            $table->boolean('mail')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
