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
        Schema::table('downloads', function (Blueprint $table) {
            $table->decimal('amount', 8, 2)->nullable()->default(0);
            $table->decimal('user_amount', 8, 2)->nullable()->default(0);
            $table->decimal('admin_amount', 8, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('downloads', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('user_amount');
            $table->dropColumn('admin_amount');
        });
    }
};
