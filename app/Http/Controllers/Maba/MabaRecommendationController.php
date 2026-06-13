<?php

namespace App\Http\Controllers\Maba;

use App\Http\Controllers\Controller;
use App\Models\Maba;
use App\Services\RecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MabaRecommendationController extends Controller
{
    public function index(): View
    {
        $maba = Maba::findOrFail(session('mit_user_id'));

        return view('maba.recommendation.index', [
            'maba' => $maba,
        ]);
    }

    public function generate(Request $request, RecommendationService $service): View|RedirectResponse
    {
        $validated = $request->validate([
            'nrp_list' => ['required', 'string'],
        ]);

        $nrpList = array_values(array_filter(array_map(
            fn ($item) => trim($item),
            explode(',', $validated['nrp_list'])
        )));

        if (count($nrpList) < 1 || count($nrpList) > 4) {
            return back()
                ->withInput()
                ->with('error', 'Masukkan 1 sampai 4 NRP, pisahkan dengan koma.');
        }

        try {
            $maba = Maba::findOrFail(session('mit_user_id'));

            return view('maba.recommendation.result', [
                'inputNrpList' => $nrpList,
                'results' => $service->recommend($maba->maba_id, $nrpList),
            ]);
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
