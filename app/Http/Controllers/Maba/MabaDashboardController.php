<?php

namespace App\Http\Controllers\Maba;

use App\Http\Controllers\Controller;
use App\Models\Maba;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MabaDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $maba = Maba::find(session('mit_user_id'));

        if (!$maba) {
            return redirect()
                ->route('mit.logout')
                ->with('error', 'Session maba tidak valid. Silakan login ulang.');
        }

        return view('maba.dashboard', [
            'maba' => $maba,
            'target' => config('mit.target_ttd'),
        ]);
    }
}
