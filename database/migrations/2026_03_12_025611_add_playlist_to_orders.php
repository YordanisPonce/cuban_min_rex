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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('play_list_id')->nullable();
            $table->unsignedBigInteger('play_list_item_id')->nullable();
            $table->foreign('play_list_id')->references('id')->on('play_lists')->cascadeOnDelete();
            $table->foreign('play_list_item_id')->references('id')->on('play_list_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['play_list_id']);
            $table->dropForeign(['play_list_item_id']);
            $table->dropColumn('play_list_id');
            $table->dropColumn('play_list_item_id');
        });
    }
};
