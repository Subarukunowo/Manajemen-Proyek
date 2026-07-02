<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('nama');                  // nama peran custom, e.g. "QA Engineer"
            $table->string('warna')->default('#0075de'); // hex color untuk badge
            $table->text('deskripsi')->nullable();   // hak akses / keterangan
            $table->boolean('is_default')->default(false); // true = dari seed awal
            $table->timestamps();

            $table->unique(['project_id', 'nama']); // tidak boleh sama dalam satu proyek
        });

        // Update project_members: kolom peran jadi string bebas (bukan enum lagi)
        Schema::table('project_members', function (Blueprint $table) {
            $table->string('peran')->change(); // dari enum ke string
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_roles');
    }
};
