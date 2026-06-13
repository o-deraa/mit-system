<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\MitAuthController;
use App\Http\Controllers\Maba\MabaDashboardController;
use App\Http\Controllers\Warga\WargaDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\KelompokWargaManagementController;
use App\Http\Controllers\Admin\MabaManagementController;
use App\Http\Controllers\Admin\MitWeekManagementController;
use App\Http\Controllers\Admin\WargaManagementController;
use App\Http\Controllers\Admin\BookingMonitoringController;
use App\Http\Controllers\Maba\MabaBookingController;
use App\Http\Controllers\Warga\WargaAvailabilityController;
use App\Http\Controllers\Warga\WargaBookingController;
use App\Http\Controllers\Warga\WargaKelompokController;
use App\Http\Controllers\Admin\RealisasiMonitoringController;
use App\Http\Controllers\Admin\VerificationWebController;
use App\Http\Controllers\Maba\MabaProgressController;
use App\Http\Controllers\Maba\MabaRealisasiController;
use App\Http\Controllers\Maba\MabaVerificationStatusController;
use App\Http\Controllers\Admin\MongoLogController;
use App\Http\Controllers\Admin\QueueMonitoringController;
use App\Http\Controllers\Maba\MabaHistoryController;
use App\Http\Controllers\Maba\MabaRecommendationController;

Route::get('/', function () {
    return redirect()->route('mit.login');
});

Route::get('/mit/login', [MitAuthController::class, 'showLogin'])
    ->name('mit.login');

Route::post('/mit/login', [MitAuthController::class, 'login'])
    ->name('mit.login.post');

Route::post('/mit/logout', [MitAuthController::class, 'logout'])
    ->name('mit.logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware('mit.role:admin')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('booking', [BookingMonitoringController::class, 'index'])->name('booking.index');
        Route::get('booking/{booking}', [BookingMonitoringController::class, 'show'])->name('booking.show');

        Route::resource('maba', MabaManagementController::class)
            ->except(['show']);

        Route::resource('warga', WargaManagementController::class)
            ->except(['show']);

        Route::resource('kelompok-warga', KelompokWargaManagementController::class);

        Route::post(
            'kelompok-warga/{kelompokWarga}/members',
            [KelompokWargaManagementController::class, 'addMember']
        )->name('kelompok-warga.members.store');

        Route::delete(
            'kelompok-warga/{kelompokWarga}/members/{memberId}',
            [KelompokWargaManagementController::class, 'removeMember']
        )->name('kelompok-warga.members.destroy');

        Route::resource('mit-week', MitWeekManagementController::class)
            ->only(['index', 'create', 'store']);

        Route::post(
            'mit-week/{weekId}/activate',
            [MitWeekManagementController::class, 'activate']
        )->name('mit-week.activate');

        Route::post(
            'mit-week/{weekId}/close',
            [MitWeekManagementController::class, 'close']
        )->name('mit-week.close');

        Route::post(
            'mit-week/{weekId}/toggle-availability',
            [MitWeekManagementController::class, 'toggleAvailability']
        )->name('mit-week.toggle-availability');

        Route::get('realisasi', [RealisasiMonitoringController::class, 'index'])
        ->name('realisasi.index');

        Route::get('realisasi/{realisasi}', [RealisasiMonitoringController::class, 'show'])
            ->name('realisasi.show');

        Route::get('verification', [VerificationWebController::class, 'index'])
            ->name('verification.index');

        Route::get('verification/requests', [VerificationWebController::class, 'requests'])
            ->name('verification.requests');

        Route::get('verification/{verification}', [VerificationWebController::class, 'show'])
            ->name('verification.show');

        Route::post('verification/{verification}/process', [VerificationWebController::class, 'process'])
            ->name('verification.process');

        Route::get('queue', [QueueMonitoringController::class, 'index'])
        ->name('queue.index');

        Route::get('logs', [MongoLogController::class, 'index'])
            ->name('logs.index');

        Route::get('logs/activity', [MongoLogController::class, 'activity'])
            ->name('logs.activity');

        Route::get('logs/recommendation', [MongoLogController::class, 'recommendation'])
            ->name('logs.recommendation');

        Route::get('logs/revision', [MongoLogController::class, 'revision'])
            ->name('logs.revision');
    });

Route::prefix('warga')
    ->name('warga.')
    ->middleware('mit.role:warga')
    ->group(function () {
        Route::get('/dashboard', [WargaDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('availability/edit', [WargaAvailabilityController::class, 'edit'])->name('availability.edit');
        Route::post('availability', [WargaAvailabilityController::class, 'update'])->name('availability.update');
        Route::get('kelompok-saya', [WargaKelompokController::class, 'show'])
    ->name('kelompok.show');
        Route::get('booking/incoming', [WargaBookingController::class, 'incoming'])->name('booking.incoming');
        Route::get('booking/accepted', [WargaBookingController::class, 'accepted'])->name('booking.accepted');
        Route::get('booking/history', [WargaBookingController::class, 'history'])->name('booking.history');
        Route::get('booking/{booking}', [WargaBookingController::class, 'show'])->name('booking.show');
        Route::post('booking/{booking}/accept', [WargaBookingController::class, 'accept'])->name('booking.accept');
        Route::post('booking/{booking}/cancel', [WargaBookingController::class, 'cancel'])->name('booking.cancel');
    });

Route::prefix('maba')
    ->name('maba.')
    ->middleware('mit.role:maba')
    ->group(function () {
        Route::get('/dashboard', [MabaDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('booking/available-groups', [MabaBookingController::class, 'availableGroups'])->name('booking.available-groups');
        Route::post('booking', [MabaBookingController::class, 'store'])->name('booking.store');
        Route::get('booking/joinable', [MabaBookingController::class, 'joinable'])->name('booking.joinable');
        Route::post('booking/join', [MabaBookingController::class, 'join'])->name('booking.join');
        Route::get('booking/mine', [MabaBookingController::class, 'mine'])->name('booking.mine');
        Route::get('booking/{booking}', [MabaBookingController::class, 'show'])->name('booking.show');
        Route::post('booking/{booking}/leave', [MabaBookingController::class, 'leave'])->name('booking.leave');
        Route::get('booking/{booking}/final-schedule', [MabaBookingController::class, 'editFinalSchedule'])->name('booking.final-schedule.edit');
        Route::put('booking/{booking}/final-schedule', [MabaBookingController::class, 'updateFinalSchedule'])->name('booking.final-schedule.update');
        Route::get('realisasi/create', [MabaRealisasiController::class, 'create'])
            ->name('realisasi.create');

        Route::post('realisasi', [MabaRealisasiController::class, 'store'])
            ->name('realisasi.store');

        Route::get('realisasi/{realisasi}', [MabaRealisasiController::class, 'show'])
            ->name('realisasi.show');

        Route::get('progress', [MabaProgressController::class, 'index'])
            ->name('progress.index');

        Route::get('verification-status', [MabaVerificationStatusController::class, 'index'])
            ->name('verification.index');

        Route::get('recommendation', [MabaRecommendationController::class, 'index'])
            ->name('recommendation.index');

        Route::post('recommendation', [MabaRecommendationController::class, 'generate'])
            ->name('recommendation.generate');

        Route::get('history', [MabaHistoryController::class, 'index'])
            ->name('history.index');

        Route::get('history/check', [MabaHistoryController::class, 'check'])
            ->name('history.check');
    });
