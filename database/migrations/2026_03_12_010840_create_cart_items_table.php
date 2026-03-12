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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('file_id')->nullable();
            $table->unsignedBigInteger('play_list_id')->nullable();
            $table->unsignedBigInteger('play_list_item_id')->nullable();
            $table->decimal('amount', 8, 2);
            $table->timestamps();
            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('file_id')->references('id')->on('files')->cascadeOnDelete();
            $table->foreign('play_list_id')->references('id')->on('play_lists')->cascadeOnDelete();
            $table->foreign('play_list_item_id')->references('id')->on('play_list_items')->cascadeOnDelete();
        });

        Schema::table('downloads', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable()->change();
            $table->unsignedBigInteger('play_list_id')->nullable();
            $table->unsignedBigInteger('play_list_item_id')->nullable();
            $table->foreign('play_list_id')->references('id')->on('play_lists')->cascadeOnDelete();
            $table->foreign('play_list_item_id')->references('id')->on('play_list_items')->cascadeOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable()->change();
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
        Schema::dropIfExists('cart_items');

        Schema::table('downloads', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable(false)->change();
            $table->dropForeign(['play_list_id']);
            $table->dropForeign(['play_list_item_id']);
            $table->dropColumn(['play_list_id', 'play_list_item_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable(false)->change();
            $table->dropForeign(['play_list_id']);
            $table->dropForeign(['play_list_item_id']);
            $table->dropColumn(['play_list_id', 'play_list_item_id']);
        });
    }
};
