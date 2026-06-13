<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\KelompokWarga;
use App\Models\MitWeek;
use App\Models\WeeklyAvailability;
use App\Models\Warga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Throwable;


class WargaAvailabilityController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $warga = Warga::findOrFail(session('mit_user_id'));
        $group = KelompokWarga::where('warga_id', $warga->warga_id)->first();

        if (!$group) {
            return redirect()->route('warga.dashboard')
                ->with('error', 'Hanya perwakilan kelompok yang bisa mengisi ketersediaan.');
        }

        $week = MitWeek::where('status', 'active')->first();

        if (!$week) {
            return redirect()->route('warga.dashboard')
                ->with('error', 'Belum ada minggu MIT yang aktif.');
        }

        if ($week->availability_input_status !== 'open') {
            return redirect()->route('warga.dashboard')
                ->with('error', 'Periode input ketersediaan sedang ditutup.');
        }

        $availability = WeeklyAvailability::firstOrNew([
            'week_id' => $week->week_id,
            'kelompok_warga_id' => $group->kelompok_warga_id,
        ]);

        return view('warga.availability.edit', [
            'warga' => $warga,
            'group' => $group,
            'week' => $week,
            'availability' => $availability,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_available' => ['required', 'boolean'],
            'session_mode' => ['required', 'integer', 'in:4,6'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $warga = Warga::findOrFail(session('mit_user_id'));
            $group = KelompokWarga::where('warga_id', $warga->warga_id)->first();

            if (!$group) {
                throw new RuntimeException('Hanya perwakilan kelompok yang bisa mengisi ketersediaan.');
            }

            $week = MitWeek::where('status', 'active')->first();

            if (!$week) {
                throw new RuntimeException('Belum ada minggu MIT yang aktif.');
            }

            if ($week->availability_input_status !== 'open') {
                throw new RuntimeException('Periode input ketersediaan sedang ditutup.');
            }

            $sessionMode = (int) $validated['session_mode'];
            $sessionCount = $sessionMode === 4 ? 3 : 2;

            WeeklyAvailability::updateOrCreate(
                [
                    'week_id' => $week->week_id,
                    'kelompok_warga_id' => $group->kelompok_warga_id,
                ],
                [
                    'is_available' => (bool) $validated['is_available'],
                    'session_mode' => $sessionMode,
                    'session_count' => $sessionCount,
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return redirect()
                ->route('warga.dashboard')
                ->with('success', 'Ketersediaan mingguan berhasil disimpan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
