<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\RestockApprovalController;
use App\Http\Controllers\Admin\VillageController;
use App\Http\Controllers\Admin\DiseaseTypeController;
use App\Http\Controllers\Admin\VaccineController;
use App\Http\Controllers\Apotek\MedicineController;
use App\Http\Controllers\Apotek\TransactionController;
use App\Http\Controllers\Apotek\RestockRequestController;
use App\Http\Controllers\Apotek\SupplierController;
use App\Http\Controllers\Kesehatan\DiseaseReportController;
use App\Http\Controllers\Kesehatan\VerificationController;
use App\Http\Controllers\Dokter\TreatmentController;
use App\Http\Controllers\Imunisasi\ChildController;
use App\Http\Controllers\Imunisasi\ScheduleController;
use App\Http\Controllers\Imunisasi\ReminderController;
use App\Http\Controllers\KepalaPuskesmas\ReportController;
use App\Http\Controllers\Warga\WargaMedicineController;
use App\Http\Controllers\Warga\WargaComplaintController;
use App\Http\Controllers\Kesehatan\AIChatController;

// Public homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // AI Chat Routes
    Route::get('ai-chat', [AIChatController::class, 'index'])->name('ai-chat.index');
    Route::post('ai-chat/send', [AIChatController::class, 'sendMessage'])->name('ai-chat.send');

    // BPS Statistics Route
    Route::get('statistik-bps', [\App\Http\Controllers\BpsController::class, 'index'])->name('bps.index');

    // Admin Group
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('villages', VillageController::class);
        Route::resource('disease-types', DiseaseTypeController::class);
        Route::resource('vaccines', VaccineController::class);
        Route::get('logs', [AuditLogController::class, 'index'])->name('logs');
        Route::delete('logs/clear', [AuditLogController::class, 'clearAll'])->name('logs.clearAll');
        Route::delete('logs/{id}', [AuditLogController::class, 'destroy'])->name('logs.destroy');
        Route::get('restock-approvals', [RestockApprovalController::class, 'index'])->name('restock.index');
        Route::post('restock-approvals/{id}/{action}', [RestockApprovalController::class, 'process'])->name('restock.process');
    });

    // Apoteker Group
    Route::middleware('role:apoteker,admin')->prefix('apotek')->name('apotek.')->group(function () {
        Route::resource('medicines', MedicineController::class);
        Route::delete('transactions/clear', [TransactionController::class, 'clearAll'])->name('transactions.clearAll');
        Route::resource('transactions', TransactionController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('restock-requests', RestockRequestController::class);
        Route::get('reports', [MedicineController::class, 'reports'])->name('reports');
    });

    // Petugas Kesehatan Group
    Route::middleware('role:petugas_medis,admin')->prefix('kesehatan')->name('kesehatan.')->group(function () {
        Route::get('reports/export', [DiseaseReportController::class, 'export'])->name('reports.export');
        Route::get('reports/template', [DiseaseReportController::class, 'template'])->name('reports.template');
        Route::post('reports/import', [DiseaseReportController::class, 'import'])->name('reports.import');
        Route::resource('reports', DiseaseReportController::class);
        Route::get('verification', [VerificationController::class, 'index'])->name('verification.index');
        Route::post('verification/{id}/{action}', [VerificationController::class, 'process'])->name('verification.process');
        Route::get('disease-map', [DiseaseReportController::class, 'map'])->name('map');
    });

    // Dokter Group
    Route::middleware('role:petugas_medis,admin')->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('consultations', [TreatmentController::class, 'index'])->name('consultations.index');
        Route::post('consultations/{id}/recommendation', [TreatmentController::class, 'recommend'])->name('consultations.recommend');
        Route::post('complaints/{id}/respond', [TreatmentController::class, 'respondToComplaint'])->name('complaints.respond');
    });

    // Petugas Imunisasi Group
    Route::middleware('role:petugas_medis,admin')->prefix('imunisasi')->name('imunisasi.')->group(function () {
        Route::resource('children', ChildController::class);
        Route::resource('schedules', ScheduleController::class);
        Route::get('reminders', [ReminderController::class, 'index'])->name('reminders.index');
        Route::post('reminders/{id}/send', [ReminderController::class, 'send'])->name('reminders.send');
    });

    // Kepala Puskesmas Group
    Route::middleware('role:admin')->prefix('kepala')->name('kepala.')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::get('kepala/reports/export/{module}/{format}', [ReportController::class, 'export'])
        ->middleware('role:admin,apoteker')
        ->name('kepala.reports.export');

    Route::middleware('role:warga')->prefix('warga')->name('warga.')->group(function () {
        Route::get('my-children', [ChildController::class, 'myChildren'])->name('children.index');
        Route::get('immunization-history/{child_id}', [ScheduleController::class, 'history'])->name('history');
        Route::post('immunization-records/{id}/complaint', [ScheduleController::class, 'reportComplaint'])->name('complaint.store');
        Route::get('medicines', [WargaMedicineController::class, 'index'])->name('medicines.index');
        Route::get('complaints', [WargaComplaintController::class, 'index'])->name('complaints.index');
    });
});
