<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('city');
            $table->enum('frequency', ['hourly', 'daily']);
            $table->string('confirmation_token')->unique()->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('unsubscribe_token')->unique()->nullable();
            $table->timestamps();

            $table->unique(['email', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
