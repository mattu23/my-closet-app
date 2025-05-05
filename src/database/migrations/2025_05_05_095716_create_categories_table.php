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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('カテゴリー名（例：トップス、Tシャツ）');
            $table->string('slug')->unique()->comment('URL用の文字列（例：tops, t-shirts）。スペースはハイフンに変換され、日本語はローマ字に変換される');
            $table->text('description')->nullable()->comment('カテゴリーの説明文');
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade')->comment('親カテゴリーのID。トップレベルの場合はnull');
            $table->string('path')->nullable()->comment('階層パス（例：1/5/8）。親カテゴリーのIDを/で区切って表現。トップレベルの場合はnull');
            $table->integer('level')->default(1)->comment('階層の深さ（例：トップレベルは1、その子は2、孫は3）');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}; 