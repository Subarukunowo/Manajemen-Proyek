<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('nama');
            $table->unsignedInteger('urutan')->default(1)->comment('Display/execution order sequence');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['Pending', 'On_Progress', 'Completed'])->default('Pending');
            $table->timestamps();

            $table->index(['project_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phases');
    }
};