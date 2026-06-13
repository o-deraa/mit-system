<?php

namespace App\Http\Controllers\Maba;

use App\Http\Controllers\Controller;
use App\Models\Maba;
use App\Models\VerificationResult;
use Illuminate\View\View;

class MabaVerificationStatusController extends Controller
{
    public function index(): View
    {
        $maba = Maba::findOrFail(session('mit_user_id'));

        return view('maba.verification.index', [
            'items' => VerificationResult::with(['realisasi.booking.group', 'week'])
                ->where('maba_id', $maba->maba_id)
                ->latest()
                ->paginate(10),
        ]);
    }
}
