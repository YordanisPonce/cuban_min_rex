<?php

use App\Enums\FolderTypeEnum;
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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('type')->default(FolderTypeEnum::PLAYLIST->value)->nullable();
            $table->timestamps();
        });

        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable()->after('id');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('set null');
        });

        Schema::table('play_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('folder_id')->nullable()->after('id');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');

        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });

        Schema::table('play_lists', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });

    }
};
