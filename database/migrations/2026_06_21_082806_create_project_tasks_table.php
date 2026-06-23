<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('phase_id')->constrained('project_phases')->restrictOnDelete();
            $table->unsignedBigInteger('parent_task_id')->nullable();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('durasi_hari')->default(1);
            $table->unsignedTinyInteger('persen_selesai')->default(0);
            $table->enum('status', ['Todo', 'In_Progress', 'Blocked', 'Done'])->default('Todo');
            $table->enum('prioritas', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('parent_task_id')->references('id')->on('project_tasks')->nullOnDelete();
            $table->index(['project_id', 'phase_id']);
            $table->index(['status', 'prioritas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};