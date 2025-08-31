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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_id');
            $table->foreignId('user_id');
            $table->foreignId('category_id');
            $table->string('title');
            $table->string('slug');
            $table->text('description');
            $table->string('keywords');
            $table->longText('body');
            $table->boolean('status')->default(1);
            $table->boolean('pinned')->default(0);
            $table->boolean('public')->default(1);
            $table->boolean('comments')->default(1);
            $table->boolean('likes')->default(1);
            $table->boolean('solved')->default(0);
            $table->boolean('closed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
