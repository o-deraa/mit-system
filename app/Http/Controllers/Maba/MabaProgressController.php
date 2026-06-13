<?php

namespace App\Http\Controllers\Maba;

use App\Http\Controllers\Controller;
use App\Models\Maba;
use App\Services\Web\TtdProgressWebService;
use Illuminate\View\View;

class MabaProgressController extends Controller
{
    public function index(TtdProgressWebService $service): View
    {
        $maba = Maba::findOrFail(session('mit_user_id'));

        return view('maba.progress.index', [
            'maba' => $maba,
            'progress' => $service->progress($maba->maba_id),
            'weeklyRecap' => $service->weeklyRecap($maba->maba_id),
        ]);
    }
}
