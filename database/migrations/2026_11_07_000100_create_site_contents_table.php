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
        Schema::create('site_contents', function (Blueprint $table) {
            $table->id();
            $table->string('hero_kicker', 160)->nullable();
            $table->string('hero_title', 160)->nullable();
            $table->text('hero_intro')->nullable();
            $table->string('about_title', 160)->nullable();
            $table->text('about_text')->nullable();
            $table->string('gallery_title', 160)->nullable();
            $table->text('gallery_intro')->nullable();
            $table->string('contact_title', 160)->nullable();
            $table->text('contact_intro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_contents');
    }
};
