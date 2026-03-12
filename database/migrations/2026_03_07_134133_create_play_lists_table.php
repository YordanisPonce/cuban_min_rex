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
        Schema::create('play_lists', function (Blueprint $table) {
            $table->id();
            $table->string('cover')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_public')->default(false);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('play_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('play_list_id')->nullable();
            $table->string('file_path')->nullable();
            $table->string('title')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('cover')->nullable();
            $table->timestamps();
            $table->foreign('play_list_id')->references('id')->on('play_lists')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_list_items');
        Schema::dropIfExists('play_lists');
    }
};
