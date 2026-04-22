<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // 🔗 Relations
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained()->onDelete('cascade');

            // 📌 Notification Content
            $table->string('title');
            $table->text('message');

            // 🧠 Type (important for future logic)
            $table->string('type'); 
            // example: weak_topic, ai_generated, absent, weekly_revision

            // ✅ Status
            $table->boolean('is_done')->default(false);

            // 🕒 Metadata
            $table->timestamp('due_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};