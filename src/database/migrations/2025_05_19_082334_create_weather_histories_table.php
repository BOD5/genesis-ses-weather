<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_histories', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->float('temperature');
            $table->integer('humidity');
            $table->string('description');
            $table->string('icon')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamp('recorded_at')->useCurrent();

            $table->index('city');
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_histories');
    }
};
