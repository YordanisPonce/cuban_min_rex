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
        Schema::create('legal_texts', function (Blueprint $table) {
            $table->id();
            $table->text('cookies')->nullable();
            $table->text('terms')->nullable();
            $table->text('privacy')->nullable();
            $table->text('legal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_texts');
    }
};
