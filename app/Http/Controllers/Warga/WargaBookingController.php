<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\KelompokWarga;
use App\Models\Warga;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class WargaBookingController extends Controller
{
    public function incoming(): View|RedirectResponse
    {
        $warga = Warga::findOrFail(session('mit_user_id'));
        $group = KelompokWarga::where('warga_id', $warga->warga_id)->first();

        if (!$group) {
            return redirect()->route('warga.dashboard')
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
        $warga = Warga::findOrFail(session('mit_user_id'));

        return view('warga.booking.show', [
            'booking' => Booking::with(['creator', 'participants.maba', 'group.representative', 'week'])
                ->findOrFail($booking),
            'warga' => $warga,
        ]);
    }

    public function accept(Request $request, int $booking, BookingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'warga_notes' => ['nullable', 'string'],
        ]);

        try {
            $warga = Warga::findOrFail(session('mit_user_id'));

            $service->acceptBookingWithoutSchedule(
                $warga,
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
            $warga = Warga::findOrFail(session('mit_user_id'));

            $service->cancelBooking($warga, $booking, $validated['cancelled_reason']);

            return back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function accepted(): View|RedirectResponse
    {
        $warga = Warga::findOrFail(session('mit_user_id'));
        $group = KelompokWarga::where('warga_id', $warga->warga_id)->first();

        if (!$group) {
            return redirect()->route('warga.dashboard')
                ->with('error', 'Hanya perwakilan kelompok yang bisa melihat jadwal accepted.');
        }

        return view('warga.booking.accepted', [
            'bookings' => Booking::with(['creator', 'participants.maba', 'week'])
                ->where('kelompok_warga_id', $group->kelompok_warga_id)
                ->where('status', 'accepted')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function history(): View|RedirectResponse
    {
        $warga = Warga::findOrFail(session('mit_user_id'));
        $group = KelompokWarga::where('warga_id', $warga->warga_id)->first();

        if (!$group) {
            return redirect()->route('warga.dashboard')
                ->with('error', 'Hanya perwakilan kelompok yang bisa melihat riwayat booking.');
        }

        return view('warga.booking.history', [
            'bookings' => Booking::with(['creator', 'participants.maba', 'week'])
                ->where('kelompok_warga_id', $group->kelompok_warga_id)
                ->whereIn('status', ['cancelled', 'completed'])
                ->latest()
                ->paginate(10),
        ]);
    }
}
