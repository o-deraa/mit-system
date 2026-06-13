<?php

namespace App\Http\Controllers\Maba;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Maba;
use App\Models\Realisasi;
use App\Services\Web\MabaRealisasiWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MabaRealisasiController extends Controller
{
    private function currentMaba(): Maba
    {
        return Maba::findOrFail(session('mit_user_id'));
    }

    public function create(Request $request): View
    {
        $maba = $this->currentMaba();

        $bookingId = (int) $request->query('booking_id');

        $booking = Booking::with(['participants.maba', 'group.members.warga', 'week', 'realisasi'])
            ->where('booking_id', $bookingId)
            ->where('status', 'accepted')
            ->whereHas('participants', function ($query) use ($maba) {
                $query->where('maba_id', $maba->maba_id)
                    ->where('status', 'joined');
            })
            ->firstOrFail();

        return view('maba.realisasi.create', [
            'booking' => $booking,
        ]);
    }

    public function store(Request $request, MabaRealisasiWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'integer', 'exists:booking,booking_id'],
            'is_warga_as_planned' => ['required', 'boolean'],
            'absent_warga_notes' => ['nullable', 'string'],
            'additional_warga_notes' => ['nullable', 'string'],
            'general_notes' => ['nullable', 'string'],
            'present_maba_ids' => ['required', 'array', 'min:1'],
            'present_maba_ids.*' => ['integer', 'exists:maba,maba_id'],
            'claims' => ['required', 'array'],
            'claims.*.claimed_ttd_2022' => ['required', 'integer', 'min:0'],
            'claims.*.claimed_ttd_2023' => ['required', 'integer', 'min:0'],
            'claims.*.claimed_ttd_2024' => ['required', 'integer', 'min:0'],
            'upload_bukti' => ['nullable', 'file', 'image', 'max:4096'],
            'upload_notes' => ['nullable', 'string'],
        ]);

        try {
            $realisasi = $service->submit(
                $this->currentMaba(),
                (int) $validated['booking_id'],
                $validated,
                $request->file('upload_bukti')
            );

            return redirect()
                ->route('maba.realisasi.show', $realisasi->realisasi_id)
                ->with('success', 'Realisasi berhasil diajukan. Menunggu verifikasi admin.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $realisasi): View
    {
        return view('maba.realisasi.show', [
            'realisasi' => Realisasi::with([
                'booking.group.representative',
                'booking.participants.maba',
                'verificationResults.maba',
            ])->findOrFail($realisasi),
        ]);
    }
}
