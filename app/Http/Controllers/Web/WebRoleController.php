<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebRoleController extends Controller
{
    private const MAX_ROLES = 15;

    // Default roles yang selalu ada (tidak bisa dihapus)
    private const DEFAULT_ROLES = [
        ['nama' => 'Owner',           'warna' => '#7e22ce', 'deskripsi' => 'Akses penuh + hapus proyek'],
        ['nama' => 'Project_Manager', 'warna' => '#0075de', 'deskripsi' => 'Kelola semua modul + tim'],
        ['nama' => 'Developer',       'warna' => '#0f766e', 'deskripsi' => 'Update task sendiri'],
        ['nama' => 'Auditor',         'warna' => '#dd5b00', 'deskripsi' => 'Hanya lihat (read-only)'],
        ['nama' => 'Stakeholder',     'warna' => '#615d59', 'deskripsi' => 'Hanya lihat dashboard'],
    ];

    public function index(Project $project): View
    {
        // Seed default roles jika belum ada
        if ($project->roles()->count() === 0) {
            foreach (self::DEFAULT_ROLES as $r) {
                $project->roles()->create([...$r, 'is_default' => true]);
            }
        }

        $roles = $project->roles()->orderBy('is_default', 'desc')->orderBy('nama')->get();
        return view('projects.roles.index', compact('project', 'roles'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        // Cek batas 15 roles
        if ($project->roles()->count() >= self::MAX_ROLES) {
            return back()->withErrors(['nama' => 'Maksimal ' . self::MAX_ROLES . ' peran per proyek.']);
        }

        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:50',
                            \Illuminate\Validation\Rule::unique('project_roles')->where('project_id', $project->id)],
            'warna'     => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $project->roles()->create([...$data, 'is_default' => false]);

        return redirect()->route('projects.roles.index', $project)
            ->with('success', 'Peran berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectRole $role): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:50',
                            \Illuminate\Validation\Rule::unique('project_roles')
                                ->where('project_id', $project->id)
                                ->ignore($role->id)],
            'warna'     => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        // Tidak boleh rename default role
        if ($role->is_default) {
            unset($data['nama']);
        }

        $role->update($data);

        return redirect()->route('projects.roles.index', $project)
            ->with('success', 'Peran berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectRole $role): RedirectResponse
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($role->is_default) {
            return back()->withErrors(['general' => 'Peran default tidak dapat dihapus.']);
        }

        // Cek apakah peran sedang dipakai anggota
        $inUse = $project->members()->where('peran', $role->nama)->exists();
        if ($inUse) {
            return back()->withErrors(['general' => "Peran '{$role->nama}' masih digunakan oleh anggota. Ubah peran anggota tersebut terlebih dahulu."]);
        }

        $role->delete();

        return redirect()->route('projects.roles.index', $project)
            ->with('success', 'Peran berhasil dihapus.');
    }
}
