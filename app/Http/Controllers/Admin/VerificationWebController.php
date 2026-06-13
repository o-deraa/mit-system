<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MitWeek;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class VerificationWebController extends Controller
{
    public function index(): View
    {
        return view('admin.verification.index', [
            'weeks' => MitWeek::orderBy('week_number')->get(),
        ]);
    }

    public function requests(Request $request, VerificationService $service): View|RedirectResponse
    {
        $validated = $request->validate([
            'week_number' => ['required', 'integer', 'min:1'],
        ]);

        try {
            return view('admin.verification.requests', [
                'weekNumber' => (int) $validated['week_number'],
                'requests' => $service->pendingRequestsByWeekNumber((int) $validated['week_number']),
            ]);
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(int $verification, VerificationService $service): View
    {
        return view('admin.verification.show', [
            'detail' => $service->detailRequest($verification),
        ]);
    }

    public function process(Request $request, int $verification, VerificationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:verified,revision,rejected'],
            'verified_ttd_2022' => ['nullable', 'integer', 'min:0'],
            'verified_ttd_2023' => ['nullable', 'integer', 'min:0'],
            'verified_ttd_2024' => ['nullable', 'integer', 'min:0'],
            'admin_comment' => ['nullable', 'string'],
        ]);

        try {
            $status = $validated['status'];

            $verified2022 = $status === 'verified' ? (int) ($validated['verified_ttd_2022'] ?? 0) : 0;
            $verified2023 = $status === 'verified' ? (int) ($validated['verified_ttd_2023'] ?? 0) : 0;
            $verified2024 = $status === 'verified' ? (int) ($validated['verified_ttd_2024'] ?? 0) : 0;

            $service->verifyRequestById(
                (string) session('mit_admin_identifier'),
                $verification,
                $status,
                $verified2022,
                $verified2023,
                $verified2024,
                $validated['admin_comment'] ?? null
            );

            return redirect()
                ->route('admin.verification.index')
                ->with('success', 'Request verifikasi berhasil diproses.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
