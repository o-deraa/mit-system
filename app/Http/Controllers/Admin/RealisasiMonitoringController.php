<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Realisasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RealisasiMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $query = Realisasi::with([
            'booking.group.representative',
            'booking.creator',
            'submitter',
            'week',
            'verificationResults.maba',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('week_id')) {
            $query->where('week_id', $request->input('week_id'));
        }

        return view('admin.realisasi.index', [
            'items' => $query->paginate(10)->withQueryString(),
            'status' => $request->input('status'),
            'weekId' => $request->input('week_id'),
        ]);
    }

    public function show(int $realisasi): View
    {
        return view('admin.realisasi.show', [
            'realisasi' => Realisasi::with([
                'booking.group.representative',
                'booking.group.members.warga',
                'booking.creator',
                'booking.participants.maba',
                'submitter',
                'week',
                'verificationResults.maba',
            ])->findOrFail($realisasi),
        ]);
    }
}
