<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->text('deskripsi_risiko');
            $table->enum('probabilitas', ['Low', 'Medium', 'High']);
            $table->enum('dampak', ['Low', 'Medium', 'High']);
            $table->unsignedTinyInteger('skor_risiko')->default(0)->comment('Calculated matrix: probabilitas * dampak');
            $table->text('mitigasi')->nullable();
            $table->enum('status', ['Identified', 'Mitigated', 'Occurred', 'Closed'])->default('Identified');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_risks');
    }
};