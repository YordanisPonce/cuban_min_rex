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
        Schema::table('billings', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('postal')->nullable()->change();
            $table->string('country')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('postal')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
        });
    }
};
