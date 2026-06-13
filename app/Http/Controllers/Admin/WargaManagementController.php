<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Services\Admin\WargaAdminWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class WargaManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Warga::with('membership.group');

        if ($request->filled('q')) {
            $keyword = $request->string('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nrp', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->input('angkatan'));
        }

        return view('admin.warga.index', [
            'wargaList' => $query->orderBy('angkatan')->orderBy('nama')->paginate(10)->withQueryString(),
            'q' => $request->input('q'),
            'angkatan' => $request->input('angkatan'),
        ]);
    }

    public function create(): View
    {
        return view('admin.warga.create');
    }

    public function store(Request $request, WargaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255', 'unique:warga,nrp'],
            'angkatan' => ['required', 'integer', 'in:2022,2023,2024'],
            'password' => ['nullable', 'string', 'min:4'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $service->create($validated);

            return redirect()
                ->route('admin.warga.index')
                ->with('success', 'Data warga berhasil ditambahkan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $warga): View
    {
        return view('admin.warga.edit', [
            'warga' => Warga::findOrFail($warga),
        ]);
    }

    public function update(Request $request, int $warga, WargaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255', 'unique:warga,nrp,' . $warga . ',warga_id'],
            'angkatan' => ['required', 'integer', 'in:2022,2023,2024'],
            'password' => ['nullable', 'string', 'min:4'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $service->update($warga, $validated);

            return redirect()
                ->route('admin.warga.index')
                ->with('success', 'Data warga berhasil diubah.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $warga, WargaAdminWebService $service): RedirectResponse
    {
        try {
            $service->deleteIfSafe($warga);

            return redirect()
                ->route('admin.warga.index')
                ->with('success', 'Data warga berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
