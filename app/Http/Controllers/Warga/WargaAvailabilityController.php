<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\MitWeek;
use App\Models\Warga;
use App\Models\WeeklyAvailability;
use App\Repositories\BookingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WargaAvailabilityController extends Controller
{
    private function currentWarga(): Warga
    {
        return Warga::with('representedGroup')->findOrFail(session('mit_user_id'));
    }

    public function edit(BookingRepository $bookingRepository): View
    {
        $warga = $this->currentWarga();
        $group = $warga->representedGroup;

        if (!$group) {
            abort(403, 'Hanya perwakilan kelompok yang bisa input availability.');
        }

        $week = $bookingRepository->activeWeek();

        if (!$week) {
            abort(404, 'Tidak ada minggu MIT aktif.');
        }

        $availability = WeeklyAvailability::firstOrNew([
            'week_id' => $week->week_id,
            'kelompok_warga_id' => $group->kelompok_warga_id,
        ], [
            'is_available' => true,
            'session_mode' => 4,
            'session_count' => 3,
            'notes' => null,
        ]);

        return view('warga.availability.edit', [
            'warga' => $warga,
            'group' => $group,
            'week' => $week,
            'availability' => $availability,
        ]);
    }

    public function update(Request $request, BookingRepository $bookingRepository): RedirectResponse
    {
        $validated = $request->validate([
            'is_available' => ['required', 'boolean'],
            'session_mode' => ['required', 'integer', 'in:4,6'],
            'session_count' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $sessionMode = (int) $validated['session_mode'];
        $sessionCount = (int) $validated['session_count'];

        $maxSessionCount = match ($sessionMode) {
            4 => 3,
            6 => 2,
            default => 0,
        };

        if ($sessionCount > $maxSessionCount) {
            return back()
                ->withInput()
                ->with('error', "Jika memilih {$sessionMode} maba per sesi, jumlah sesi maksimal adalah {$maxSessionCount}.");
        }

        $warga = $this->currentWarga();
        $group = $warga->representedGroup;

        if (!$group) {
            abort(403, 'Hanya perwakilan kelompok yang bisa input availability.');
        }

        $week = $bookingRepository->activeWeek();

        if (!$week) {
            return back()->with('error', 'Tidak ada minggu MIT aktif.');
        }

        if ($week->availability_input_status !== 'open') {
            return back()->with('error', 'Periode input availability sedang ditutup admin.');
        }

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
            ->with('success', 'Availability berhasil disimpan.');
    }
}
