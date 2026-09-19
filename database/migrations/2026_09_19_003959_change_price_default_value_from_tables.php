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
        Schema::table('files', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->default('4.99')->change();
        });

        Schema::table('play_list_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->default('4.99')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->default('0')->change();
        });

        Schema::table('play_list_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->default('0')->change();
        });
    }
};
