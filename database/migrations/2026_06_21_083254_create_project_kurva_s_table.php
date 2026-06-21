<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_kurva_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->date('periode')->comment('First or last day of the reporting week/month');
            $table->decimal('rencana_kumulatif', 5, 2)->default(0)->comment('Target progress percent e.g. 45.50');
            $table->decimal('realisasi_kumulatif', 5, 2)->default(0)->comment('Actual progress percent e.g. 42.10');
            $table->decimal('rencana_periode', 5, 2)->default(0)->comment('Incremental target for single period');
            $table->decimal('realisasi_periode', 5, 2)->default(0)->comment('Incremental actual for single period');
            $table->timestamps();

            $table->unique(['project_id', 'periode']);
            $table->index(['project_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_kurva_s');
    }
};