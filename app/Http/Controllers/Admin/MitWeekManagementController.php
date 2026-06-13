<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MitWeek;
use App\Services\Admin\MitWeekAdminWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MitWeekManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.mit-week.index', [
            'weeks' => MitWeek::orderBy('week_number')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.mit-week.create');
    }

    public function store(Request $request, MitWeekAdminWebService $service): RedirectResponse
    {
        $validated = $request->validate([
            'week_number' => ['required', 'integer', 'min:1', 'unique:mit_week,week_number'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        try {
            $service->create($validated);

            return redirect()
                ->route('admin.mit-week.index')
                ->with('success', 'Minggu MIT berhasil dibuat.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function activate(int $weekId, MitWeekAdminWebService $service): RedirectResponse
    {
        try {
            $service->activate($weekId);

            return back()->with('success', 'Minggu MIT berhasil diaktifkan.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function close(int $weekId, MitWeekAdminWebService $service): RedirectResponse
    {
        try {
            $service->close($weekId);

            return back()->with('success', 'Minggu MIT berhasil ditutup.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleAvailability(int $weekId, MitWeekAdminWebService $service): RedirectResponse
    {
        try {
            $service->toggleAvailabilityInput($weekId);

            return back()->with('success', 'Status input ketersediaan berhasil diubah.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
