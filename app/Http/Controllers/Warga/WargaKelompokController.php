<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\KelompokWargaMember;
use App\Models\Warga;
use Illuminate\View\View;

class WargaKelompokController extends Controller
{
    public function show(): View
    {
        $warga = Warga::findOrFail(session('mit_user_id'));

        $membership = KelompokWargaMember::with([
            'group.representative',
            'group.members.warga',
        ])
            ->where('warga_id', $warga->warga_id)
            ->first();

        return view('warga.kelompok.show', [
            'warga' => $warga,
            'membership' => $membership,
            'group' => $membership?->group,
        ]);
    }
}
