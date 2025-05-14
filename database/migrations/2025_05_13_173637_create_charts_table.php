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
        Schema::create('charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained('datasets')->onDelete('cascade');
            $table->string('title');
            $table->enum('chart_type', ['bar', 'line', 'pie', 'histogram', 'scatter_plots', 'heatmaps']);
            $table->json('variables');
            $table->string('description')->nullable();
            $table->json('metadata');
            $table->json('chart_layout')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charts');
    }
};
