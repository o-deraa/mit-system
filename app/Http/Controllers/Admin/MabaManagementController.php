<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maba;
use App\Services\Admin\MabaAdminWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MabaManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Maba::query();

        if ($request->filled('q')) {
            $keyword = $request->string('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('nrp', 'like', "%{$keyword}%");
            });
        }

        return view('admin.maba.index', [
            'mabaList' => $query
                ->orderBy('nrp', 'asc')
                ->paginate(10)
                ->withQueryString(),
            'q' => $request->input('q'),
        ]);
    }

    public function create(): View
    {
        return view('admin.maba.create');
    }

    public function store(Request $request, MabaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255', 'unique:maba,nrp'],
            'password' => ['nullable', 'string', 'min:4'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $service->create($validated);

            return redirect()
                ->route('admin.maba.index')
                ->with('success', 'Data maba berhasil ditambahkan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $maba): View
    {
        return view('admin.maba.edit', [
            'maba' => Maba::findOrFail($maba),
        ]);
    }

    public function update(Request $request, int $maba, MabaAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255', 'unique:maba,nrp,' . $maba . ',maba_id'],
            'password' => ['nullable', 'string', 'min:4'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            $service->update($maba, $validated);

            return redirect()
                ->route('admin.maba.index')
                ->with('success', 'Data maba berhasil diubah.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $maba, MabaAdminWebService $service): RedirectResponse
    {
        try {
            $service->deleteIfSafe($maba);

            return redirect()
                ->route('admin.maba.index')
                ->with('success', 'Data maba berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
