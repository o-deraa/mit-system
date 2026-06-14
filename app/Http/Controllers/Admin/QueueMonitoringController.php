<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MitWeek;
use App\Models\WeeklyAvailability;
use App\Repositories\BookingRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueMonitoringController extends Controller
{
    public function index(Request $request, BookingRepository $bookingRepository): View
    {
        $weekId = $request->input('week_id');

        $week = $weekId
            ? MitWeek::find($weekId)
            : $bookingRepository->activeWeek();

        $rows = collect();

        if ($week) {
            $rows = WeeklyAvailability::with(['group.representativeMember.warga', 'group.members.warga'])
                ->where('week_id', $week->week_id)
                ->orderBy('kelompok_warga_id')
                ->get()
                ->map(function (WeeklyAvailability $availability) use ($week, $bookingRepository) {
                    $groupId = $availability->kelompok_warga_id;

                    $pendingCount = Booking::where('week_id', $week->week_id)
                        ->where('kelompok_warga_id', $groupId)
                        ->where('status', 'pending')
                        ->count();

                    $acceptedCount = Booking::where('week_id', $week->week_id)
                        ->where('kelompok_warga_id', $groupId)
                        ->where('status', 'accepted')
                        ->count();

                    $activeQueue = $bookingRepository->activeQueueCount($week->week_id, $groupId);
                    $maxQueue = (int) $availability->session_count;

                    return [
                        'availability' => $availability,
                        'group' => $availability->group,
                        'pending_count' => $pendingCount,
                        'accepted_count' => $acceptedCount,
                        'active_queue' => $activeQueue,
                        'max_queue' => $maxQueue,
                        'sisa_queue' => max(0, $maxQueue - $activeQueue),
                    ];
                });
        }

        return view('admin.queue.index', [
            'weeks' => MitWeek::orderBy('week_number')->get(),
            'selectedWeek' => $week,
            'rows' => $rows,
        ]);
    }
}
