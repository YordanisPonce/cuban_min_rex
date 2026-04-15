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
        Schema::create('seo_texts', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->nullable()->default('CubanPool');
            $table->text('app_keywords')->nullable()->default('CubanPool, cuban pool, música, remixes, dj, descargas, mp3, sets, mixes, electrónica, edm, descargar música, escuchar música online, dj tools, producer tools, club music, dance music');
            $table->text('app_description')->nullable()->default('Escucha y descarga música, remixes exclusivos y contenido para DJs en CubanPool. La mejor plataforma para descubrir nuevos sonidos y apoyar a tus DJs favoritos.');
            $table->string('app_logo')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_instagram')->nullable();
            $table->string('contact_youtube')->nullable();
            $table->string('contact_facebook')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_texts');
    }
};
