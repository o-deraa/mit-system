<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mongo\ActivityLog;
use App\Models\Mongo\RecommendationLog;
use App\Models\Mongo\RevisionHistory;
use Illuminate\View\View;

class MongoLogController extends Controller
{
    public function index(): View
    {
        return view('admin.logs.index', [
            'activityCount' => ActivityLog::count(),
            'recommendationCount' => RecommendationLog::count(),
            'revisionCount' => RevisionHistory::count(),
        ]);
    }

    public function activity(): View
    {
        return view('admin.logs.activity', [
            'items' => ActivityLog::query()
                ->orderBy('_id', 'desc')
                ->simplePaginate(15),
        ]);
    }

    public function recommendation(): View
    {
        return view('admin.logs.recommendation', [
            'items' => RecommendationLog::query()
                ->orderBy('_id', 'desc')
                ->simplePaginate(15),
        ]);
    }

    public function revision(): View
    {
        return view('admin.logs.revision', [
            'items' => RevisionHistory::query()
                ->orderBy('_id', 'desc')
                ->simplePaginate(15),
        ]);
    }
}
