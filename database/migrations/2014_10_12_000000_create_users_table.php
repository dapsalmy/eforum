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
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('username')->nullable();
            $table->string('profession')->nullable();
            $table->string('gender')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->string('website')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('role')->default('User');
            $table->rememberToken();
            $table->timestamp('last_seen')->nullable();
            $table->decimal('wallet', 10, 2);
            $table->decimal('earnings', 10, 2);
            $table->foreignId('plan_id');
            $table->boolean('verified')->default(0)->nullable();
            $table->string('paypal_email')->nullable();
            $table->dateTime('banned_at')->nullable();
            $table->string('google_id')->unique()->nullable();
            $table->string('facebook_id')->unique()->nullable();
            $table->boolean('email_comment')->default(0)->nullable();
            $table->boolean('email_reply')->default(0)->nullable();
            $table->boolean('email_follower')->default(0)->nullable();
            $table->boolean('email_message')->default(0)->nullable();
            $table->boolean('email_tip')->default(0)->nullable();
            $table->boolean('email_funds')->default(0)->nullable();
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
