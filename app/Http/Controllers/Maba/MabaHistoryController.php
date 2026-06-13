<?php

namespace App\Http\Controllers\Maba;

use App\Http\Controllers\Controller;
use App\Models\KelompokWarga;
use App\Models\Maba;
use App\Models\MabaKelompokHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MabaHistoryController extends Controller
{
    public function index(): View
    {
        $maba = Maba::findOrFail(session('mit_user_id'));

        return view('maba.history.index', [
            'histories' => MabaKelompokHistory::with(['group.representative', 'week', 'booking'])
                ->where('maba_id', $maba->maba_id)
                ->latest('created_at')
                ->paginate(10),
        ]);
    }

    public function check(Request $request): View
    {
        $maba = Maba::findOrFail(session('mit_user_id'));

        $result = null;
        $group = null;

        if ($request->filled('kode_kelompok')) {
            $group = KelompokWarga::where('kode_kelompok', $request->input('kode_kelompok'))->first();

            if ($group) {
                $result = MabaKelompokHistory::with(['week', 'booking'])
                    ->where('maba_id', $maba->maba_id)
                    ->where('kelompok_warga_id', $group->kelompok_warga_id)
                    ->first();
            }
        }

        return view('maba.history.check', [
            'kodeKelompok' => $request->input('kode_kelompok'),
            'group' => $group,
            'result' => $result,
        ]);
    }
}
