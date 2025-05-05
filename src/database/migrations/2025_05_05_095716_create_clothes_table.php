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
        Schema::create('clothes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('所有者のユーザーID');
            $table->foreignId('category_id')->constrained()->onDelete('cascade')->comment('カテゴリーID');
            $table->string('name')->comment('洋服の名前');
            $table->string('image_path')->comment('画像のパス');
            $table->text('description')->nullable()->comment('洋服の説明');
            $table->string('brand')->nullable()->comment('ブランド名');
            $table->string('color')->nullable()->comment('色');
            $table->string('size')->nullable()->comment('サイズ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clothes');
    }
}; 