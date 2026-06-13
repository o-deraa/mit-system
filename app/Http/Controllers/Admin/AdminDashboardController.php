<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\KelompokWarga;
use App\Models\Maba;
use App\Models\MitWeek;
use App\Models\Realisasi;
use App\Models\Warga;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalMaba' => Maba::count(),
            'totalWarga' => Warga::count(),
            'totalKelompok' => KelompokWarga::count(),
            'totalWeek' => MitWeek::count(),
            'totalBooking' => Booking::count(),
            'totalRealisasi' => Realisasi::count(),
            'activeWeek' => MitWeek::where('status', 'active')->first(),
        ]);
    }
}
