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
        Schema::create('station_settings', function (Blueprint $table) {
            $table->id();
            $table->string('station_name')->nullable();
            $table->string('slogan')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('stream_url', 2048)->nullable();
            $table->string('stream_format', 10)->default('mp3');
            $table->string('stream_name')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_settings');
    }
};
