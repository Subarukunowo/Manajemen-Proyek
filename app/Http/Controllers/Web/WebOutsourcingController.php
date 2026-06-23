<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\LoadsProjectForSidebar;
use App\Models\Project;
use App\Models\ProjectOutsourcing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebOutsourcingController extends Controller
{
    use LoadsProjectForSidebar;

    public function index(Project $project): View
    {
        $this->loadProjectForSidebar($project);
        $vendors = $project->outsourcings()->orderByDesc('tanggal_mulai')->get();
        return view('projects.outsourcing.index', compact('project', 'vendors'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'nama_vendor'     => ['required', 'string', 'max:255'],
            'lingkup_kerja'   => ['nullable', 'string'],
            'nilai_kontrak'   => ['required', 'numeric', 'min:0'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status'          => ['required', 'in:Active,Completed,Terminated'],
            'persen_selesai'  => ['required', 'integer', 'min:0', 'max:100'],
        ]);
        $project->outsourcings()->create($data);

        return redirect()->route('projects.outsourcing.index', $project)
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectOutsourcing $outsourcing): RedirectResponse
    {
        $data = $request->validate([
            'nama_vendor'     => ['required', 'string', 'max:255'],
            'lingkup_kerja'   => ['nullable', 'string'],
            'nilai_kontrak'   => ['required', 'numeric', 'min:0'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status'          => ['required', 'in:Active,Completed,Terminated'],
            'persen_selesai'  => ['required', 'integer', 'min:0', 'max:100'],
        ]);
        $outsourcing->update($data);

        return redirect()->route('projects.outsourcing.index', $project)
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectOutsourcing $outsourcing): RedirectResponse
    {
        $outsourcing->delete();

        return redirect()->route('projects.outsourcing.index', $project)
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
