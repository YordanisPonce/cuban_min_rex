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
        Schema::create('download_packs', function (Blueprint $table) {
            $table->id();
            $table->integer('extra_downloads')->nullable()->default(0);
            $table->decimal('price', 8, 2)->nullable()->default('4.99');
            $table->boolean('active')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_packs');
    }
};
