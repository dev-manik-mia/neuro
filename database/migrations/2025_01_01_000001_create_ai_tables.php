<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_memories', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('role');
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('ai_documents', function (Blueprint $table) {
            $table->id();
            $table->string('collection');
            $table->string('external_id')->nullable();
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->binary('embedding')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_memories');
        Schema::dropIfExists('ai_documents');
    }
};
