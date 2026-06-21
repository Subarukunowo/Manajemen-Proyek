<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('project_tasks')->cascadeOnDelete();
            $table->string('nama_resource');
            $table->enum('tipe', ['Human', 'Material', 'Equipment']);
            $table->decimal('jumlah', 10, 2)->default(0);
            $table->string('satuan')->comment('e.g. Hours, Pcs, Units');
            $table->decimal('biaya_satuan', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0)->comment('Calculated: jumlah * biaya_satuan via Observer');
            $table->timestamps();

            $table->index('project_id');
            $table->index('task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_resources');
    }
};