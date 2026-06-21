<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('nomor_cr')->unique()->comment('Unique Change Request Number');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('status', ['Draft', 'Pending', 'Approved', 'Rejected'])->default('Draft');
            $table->enum('dampak', ['Low', 'Medium', 'High']);
            $table->decimal('biaya_perubahan', 15, 2)->default(0)->comment('Can be zero or negative');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_request');
            $table->date('tanggal_approval')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index('nomor_cr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_changes');
    }
};