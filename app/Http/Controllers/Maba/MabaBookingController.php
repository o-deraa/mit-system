<?php

namespace App\Http\Controllers\Maba;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Maba;
use App\Services\BookingQueryService;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MabaBookingController extends Controller
{
    private function currentMaba(): Maba
    {
        return Maba::findOrFail(session('mit_user_id'));
    }

    public function availableGroups(BookingQueryService $queryService): View
    {
        $maba = $this->currentMaba();

        return view('maba.booking.available-groups', [
            'maba' => $maba,
            'rows' => $queryService->availableGroupsForMaba($maba),
        ]);
    }

    public function store(Request $request, BookingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'kelompok_warga_id' => ['required', 'integer', 'exists:kelompok_warga,kelompok_warga_id'],
        ]);

        try {
            $booking = $service->createBooking(
                $this->currentMaba(),
                (int) $validated['kelompok_warga_id']
            );

            return redirect()
                ->route('maba.booking.show', $booking->booking_id)
                ->with('success', 'Request booking berhasil dibuat.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function joinable(BookingQueryService $queryService): View
    {
        $maba = $this->currentMaba();

        return view('maba.booking.joinable', [
            'maba' => $maba,
            'rows' => $queryService->joinableAcceptedBookingsForMaba($maba),
        ]);
    }

    public function join(Request $request, BookingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'integer', 'exists:booking,booking_id'],
        ]);

        try {
            $service->joinBooking($this->currentMaba(), (int) $validated['booking_id']);

            return redirect()
                ->route('maba.booking.mine')
                ->with('success', 'Berhasil bergabung ke booking accepted.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function mine(): View
    {
        $maba = $this->currentMaba();

        return view('maba.booking.my-bookings', [
            'bookings' => Booking::with(['group.representativeMember.warga', 'week', 'participants.maba', 'realisasi'])
                ->whereHas('participants', function ($query) use ($maba) {
                    $query->where('maba_id', $maba->maba_id);
                })
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(int $booking): View
    {
        return view('maba.booking.show', [
            'booking' => Booking::with(['group.representativeMember.warga', 'group.members.warga', 'week', 'participants.maba'])
                ->findOrFail($booking),
        ]);
    }

    public function leave(int $booking, BookingService $service): RedirectResponse
    {
        try {
            $service->leaveBooking($this->currentMaba(), $booking);

            return redirect()
                ->route('maba.booking.mine')
                ->with('success', 'Berhasil keluar dari booking.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editFinalSchedule(int $booking): View
    {
        return view('maba.booking.final-schedule', [
            'booking' => Booking::with(['group.representativeMember.warga', 'participants.maba'])->findOrFail($booking),
        ]);
    }

    public function updateFinalSchedule(Request $request, int $booking, BookingService $service): RedirectResponse
    {
        $validated = $request->validate([
            'final_schedule' => ['required', 'date'],
            'final_location' => ['required', 'string', 'max:255'],
        ]);

        try {
            $service->updateFinalScheduleByMaba(
                $this->currentMaba(),
                $booking,
                $validated['final_schedule'],
                $validated['final_location']
            );

        return redirect()
            ->route('maba.booking.mine')
            ->with('success', 'Jadwal final dan lokasi final berhasil disimpan.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
