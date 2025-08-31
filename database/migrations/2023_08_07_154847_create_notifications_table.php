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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id');
            $table->foreignId('recipient_id');
            $table->text('notification_type');
            $table->foreignId('post_id')->nullable();
            $table->string('like_id')->nullable();
            $table->string('comment_id')->nullable();
            $table->string('reply_id')->nullable();
            $table->string('react_id')->nullable();
            $table->string('following_id')->nullable();
            $table->string('tip_id')->nullable();
            $table->enum('seen', ['1', '2']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
