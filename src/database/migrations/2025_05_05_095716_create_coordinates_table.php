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
        Schema::create('coordinates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('所有者のユーザーID');
            $table->string('name')->comment('コーディネートの名前');
            $table->string('image_path')->comment('コーディネートの画像パス');
            $table->text('description')->nullable()->comment('コーディネートの説明');
            $table->foreignId('top_id')->nullable()->constrained('clothes')->onDelete('set null')->comment('トップスID');
            $table->foreignId('bottom_id')->nullable()->constrained('clothes')->onDelete('set null')->comment('ボトムスID');
            $table->foreignId('outer_id')->nullable()->constrained('clothes')->onDelete('set null')->comment('アウターID');
            $table->foreignId('shoes_id')->nullable()->constrained('clothes')->onDelete('set null')->comment('靴ID');
            $table->foreignId('accessory_id')->nullable()->constrained('clothes')->onDelete('set null')->comment('アクセサリーID');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinates');
    }
}; 