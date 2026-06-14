<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelompokWarga;
use App\Models\KelompokWargaMember;
use App\Models\Warga;
use App\Services\Admin\KelompokWargaAdminWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class KelompokWargaManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.kelompok-warga.index', [
            'groups' => KelompokWarga::with(['representativeMember.warga', 'members.warga'])
                ->orderBy('kode_kelompok')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.kelompok-warga.create');
    }

    public function store(Request $request, KelompokWargaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'kode_kelompok' => ['required', 'integer', 'unique:kelompok_warga,kode_kelompok'],
            'rules' => ['nullable', 'string'],
        ]);

        try {
            $group = $service->create($validated);

            return redirect()
                ->route('admin.kelompok-warga.show', $group->kelompok_warga_id)
                ->with('success', 'Kelompok warga berhasil dibuat. Silakan tambahkan anggota.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(KelompokWarga $kelompokWarga): View
    {
        return view('admin.kelompok-warga.show', [
            'group' => $kelompokWarga->load(['members.warga', 'representativeMember.warga']),
            'availableWarga' => Warga::where('status', 'active')
                ->whereDoesntHave('membership')
                ->orderBy('angkatan')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function edit(KelompokWarga $kelompokWarga): View
    {
        return view('admin.kelompok-warga.edit', [
            'group' => $kelompokWarga->load(['members.warga', 'representativeMember.warga']),
        ]);
    }

    public function update(
        Request $request,
        KelompokWarga $kelompokWarga,
        KelompokWargaAdminWebService $service
    ): RedirectResponse {
        $validated = $request->validate([
            'kode_kelompok' => ['required', 'integer', 'unique:kelompok_warga,kode_kelompok,' . $kelompokWarga->kelompok_warga_id . ',kelompok_warga_id'],
            'rules' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,final'],
        ]);

        try {
            $service->update($kelompokWarga, $validated);

            return redirect()
                ->route('admin.kelompok-warga.show', $kelompokWarga->kelompok_warga_id)
                ->with('success', 'Kelompok warga berhasil diperbarui.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(KelompokWarga $kelompokWarga): RedirectResponse
    {
        $kelompokWarga->delete();

        return redirect()
            ->route('admin.kelompok-warga.index')
            ->with('success', 'Kelompok warga berhasil dihapus.');
    }

    public function addMember(
        Request $request,
        KelompokWarga $kelompokWarga,
        KelompokWargaAdminWebService $service
    ): RedirectResponse {
        $validated = $request->validate([
            'warga_id' => ['required', 'integer', 'exists:warga,warga_id'],
            'is_perwakilan' => ['nullable', 'boolean'],
            'nomor_wa' => ['nullable', 'string', 'max:30'],
        ]);

        try {
            $service->addMember($kelompokWarga, $validated);

            return back()->with('success', 'Anggota berhasil ditambahkan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function removeMember(KelompokWargaMember $member, KelompokWargaAdminWebService $service): RedirectResponse
    {
        try {
            $service->removeMember($member);

            return back()->with('success', 'Anggota berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function setRepresentative(
        Request $request,
        KelompokWargaMember $member,
        KelompokWargaAdminWebService $service
    ): RedirectResponse {
        $validated = $request->validate([
            'nomor_wa' => ['required', 'string', 'max:30'],
        ]);

        try {
            $service->setRepresentative($member, $validated['nomor_wa']);

            return back()->with('success', 'Perwakilan kelompok berhasil diperbarui.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function finalize(KelompokWarga $kelompokWarga, KelompokWargaAdminWebService $service): RedirectResponse
    {
        try {
            $service->finalize($kelompokWarga);

            return back()->with('success', 'Kelompok berhasil difinalisasi.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
