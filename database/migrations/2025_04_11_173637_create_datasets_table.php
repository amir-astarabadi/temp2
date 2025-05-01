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
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('file_path');
            $table->enum('status', ['uploading', 'uploaded', 'inserting', 'inserted', 'processing', 'processed', 'error'])->default('uploading');
            $table->enum('type', ['csv', 'xlsx', 'xls', 'excel']);
            $table->unsignedTinyInteger('order');
            $table->timestamp('pinned_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};