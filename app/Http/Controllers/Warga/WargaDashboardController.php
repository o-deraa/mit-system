<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WargaDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $warga = Warga::find(session('mit_user_id'));

        if (!$warga) {
            return redirect()
                ->route('mit.logout')
                ->with('error', 'Session warga tidak valid. Silakan login ulang.');
        }

        return view('warga.dashboard', [
            'warga' => $warga,
        ]);
    }
}
