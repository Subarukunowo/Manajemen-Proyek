<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebMemberController extends Controller
{
    use LoadsProjectForSidebar;

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $members = $project->members()->with('user')->orderBy('peran')->get();
        $users   = User::orderBy('name')->get();
        return view('projects.members.index', compact('project', 'members', 'users'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        // Hanya admin sistem yang dapat menambah anggota
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat menambah anggota.');
        }

        $data = $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'peran'             => ['required', 'in:Owner,Project_Manager,Developer,Auditor,Stakeholder'],
            'tanggal_bergabung' => ['nullable', 'date'],
        ]);

        if ($project->members()->where('user_id', $data['user_id'])->exists()) {
            return back()->withErrors(['user_id' => 'User ini sudah menjadi anggota proyek.']);
        }

        $project->members()->create($data);

        return redirect()->route('projects.members.index', $project)
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectMember $member): RedirectResponse
    {
        // Hanya admin sistem yang dapat mengubah peran
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengubah peran anggota.');
        }

        $data = $request->validate([
            'peran'             => ['required', 'in:Owner,Project_Manager,Developer,Auditor,Stakeholder'],
            'tanggal_bergabung' => ['nullable', 'date'],
        ]);
        $member->update($data);

        return redirect()->route('projects.members.index', $project)
            ->with('success', 'Peran anggota berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectMember $member): RedirectResponse
    {
        // Hanya admin sistem yang dapat menghapus anggota
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat menghapus anggota.');
        }

        $member->delete();

        return redirect()->route('projects.members.index', $project)
            ->with('success', 'Anggota berhasil dihapus dari proyek.');
    }
}
