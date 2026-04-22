<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('ai_revision_materials', function (Blueprint $table) {
        $table->id();
        $table->foreignId('classroom_id')->constrained();
        $table->string('topic_name');
        $table->string('pdf_path');
        $table->timestamps(); // Isse 'Created Date' mil jayegi history ke liye
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_revision_materials');
    }
};
