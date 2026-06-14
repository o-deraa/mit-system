<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Warga;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class WargaBookingController extends Controller
{
    private function currentWarga(): Warga
    {
        return Warga::with('representedGroup')->findOrFail(session('mit_user_id'));
    }

    public function incoming(): View|RedirectResponse
    {
        $warga = $this->currentWarga();
        $group = $warga->representedGroup;

        if (!$group) {
            return redirect()
                ->route('warga.dashboard')
                ->with('error', 'Hanya perwakilan kelompok yang bisa melihat booking masuk.');
        }

        $bookings = Booking::with(['creator', 'participants.maba', 'week'])
            ->where('kelompok_warga_id', $group->kelompok_warga_id)
            ->whereIn('status', ['pending', 'accepted'])
            ->latest()
            ->paginate(10);

        return view('warga.booking.incoming', [
            'warga' => $warga,
            'group' => $group,
            'bookings' => $bookings,
        ]);
    }

    public function show(int $booking): View
    {
        $warga = $this->currentWarga();

        return view('warga.booking.show', [
            'booking' => Booking::with([
                'creator',
                'participants.maba',
                'group.representativeMember.warga',
                'week',
                'realisasi',
            ])->findOrFail($booking),
            'warga' => $warga,
        ]);
    }

    public function accept(Request $request, int $booking, BookingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'warga_notes' => ['nullable', 'string'],
        ]);

        try {
            $service->acceptBookingWithoutSchedule(
                $this->currentWarga(),
                $booking,
                $validated['warga_notes'] ?? null
            );

            return back()->with('success', 'Booking berhasil di-accept. Maba dapat mengisi jadwal final dan lokasi final.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, int $booking, BookingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'cancelled_reason' => ['required', 'string'],
        ]);

        try {
            $service->cancelBooking(
                $this->currentWarga(),
                $booking,
                $validated['cancelled_reason']
            );

            return back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function accepted(): View|RedirectResponse
    {
        $warga = $this->currentWarga();
        $group = $warga->representedGroup;

        if (!$group) {
            return redirect()
                ->route('warga.dashboard')
                ->with('error', 'Hanya perwakilan kelompok yang bisa melihat jadwal accepted.');
        }

        return view('warga.booking.accepted', [
            'warga' => $warga,
            'group' => $group,
            'bookings' => Booking::with(['creator', 'participants.maba', 'week'])
                ->where('kelompok_warga_id', $group->kelompok_warga_id)
                ->where('status', 'accepted')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function history(): View|RedirectResponse
    {
        $warga = $this->currentWarga();
        $group = $warga->representedGroup;

        if (!$group) {
            return redirect()
                ->route('warga.dashboard')
                ->with('error', 'Hanya perwakilan kelompok yang bisa melihat riwayat booking.');
        }

        return view('warga.booking.history', [
            'warga' => $warga,
            'group' => $group,
            'bookings' => Booking::with(['creator', 'participants.maba', 'week'])
                ->where('kelompok_warga_id', $group->kelompok_warga_id)
                ->whereIn('status', ['cancelled', 'completed'])
                ->latest()
                ->paginate(10),
        ]);
    }
}
