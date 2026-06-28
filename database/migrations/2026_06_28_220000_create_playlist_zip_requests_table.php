<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlist_zip_requests', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 40)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('play_list_id')->constrained('play_lists')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->string('zip_file_name');
            $table->string('s3_path')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('tracks_added')->default(0);
            $table->unsignedInteger('tracks_total')->default(0);
            $table->boolean('download_registered')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'play_list_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_zip_requests');
    }
};
