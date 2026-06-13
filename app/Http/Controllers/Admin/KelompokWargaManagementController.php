<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelompokWarga;
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
            'groups' => KelompokWarga::with(['representative', 'members.warga'])
                ->orderBy('kode_kelompok')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.kelompok-warga.create', [
            'floatingWarga' => $this->floatingWarga(),
        ]);
    }

    public function store(Request $request, KelompokWargaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'warga_id' => ['required', 'integer', 'exists:warga,warga_id'],
            'nomor_wa_perwakilan' => ['required', 'string', 'max:255'],
            'rules' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,final'],
            'member_ids' => ['required', 'array', 'min:1', 'max:3'],
            'member_ids.*' => ['integer', 'exists:warga,warga_id'],
        ]);

        try {
            $group = $service->create($validated);

            return redirect()
                ->route('admin.kelompok-warga.show', $group->kelompok_warga_id)
                ->with('success', 'Kelompok warga berhasil dibentuk.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $kelompokWarga): View
    {
        return view('admin.kelompok-warga.show', [
            'group' => KelompokWarga::with(['representative', 'members.warga'])->findOrFail($kelompokWarga),
            'floatingWarga' => $this->floatingWarga(),
        ]);
    }

    public function edit(int $kelompokWarga): View
    {
        return view('admin.kelompok-warga.edit', [
            'group' => KelompokWarga::with(['representative', 'members.warga'])->findOrFail($kelompokWarga),
        ]);
    }

    public function update(Request $request, int $kelompokWarga, KelompokWargaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_wa_perwakilan' => ['required', 'string', 'max:255'],
            'rules' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,final'],
        ]);

        try {
            $service->update($kelompokWarga, $validated);

            return redirect()
                ->route('admin.kelompok-warga.show', $kelompokWarga)
                ->with('success', 'Data kelompok warga berhasil diubah.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $kelompokWarga, KelompokWargaAdminWebService $service): RedirectResponse
    {
        try {
            $service->deleteIfSafe($kelompokWarga);

            return redirect()
                ->route('admin.kelompok-warga.index')
                ->with('success', 'Kelompok warga berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addMember(Request $request, int $kelompokWarga, KelompokWargaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'warga_id' => ['required', 'integer', 'exists:warga,warga_id'],
        ]);

        try {
            $service->addMember($kelompokWarga, (int) $validated['warga_id']);

            return back()->with('success', 'Anggota kelompok berhasil ditambahkan.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeMember(int $kelompokWarga, int $memberId, KelompokWargaAdminWebService $service): RedirectResponse
    {
        try {
            $service->removeMember($memberId);

            return back()->with('success', 'Anggota kelompok berhasil dikurangi.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function floatingWarga()
    {
        return Warga::where('status', 'active')
            ->whereDoesntHave('membership')
            ->orderBy('angkatan')
            ->orderBy('nama')
            ->get();
    }
}
