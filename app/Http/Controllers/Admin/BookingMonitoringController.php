<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with([
            'week',
            'group.representative',
            'creator',
            'participants.maba',
            'realisasi',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('week_id')) {
            $query->where('week_id', $request->input('week_id'));
        }

        return view('admin.booking.index', [
            'bookings' => $query->paginate(10)->withQueryString(),
            'status' => $request->input('status'),
            'weekId' => $request->input('week_id'),
        ]);
    }

    public function show(int $booking): View
    {
        return view('admin.booking.show', [
            'booking' => Booking::with([
                'week',
                'group.representative',
                'group.members.warga',
                'creator',
                'participants.maba',
                'realisasi',
            ])->findOrFail($booking),
        ]);
    }
}
