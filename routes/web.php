<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\PlatformDashboardController;
use App\Http\Controllers\App\AppointmentController;
use App\Http\Controllers\App\AuditLogController;
use App\Http\Controllers\App\ConsultantController;
use App\Http\Controllers\App\ConsultantPermissionController;
use App\Http\Controllers\App\DocumentController;
use App\Http\Controllers\App\PatientController;
use App\Http\Controllers\App\ReportController;
use App\Http\Controllers\Patient\PatientAppointmentController;
use App\Http\Controllers\Patient\PatientDocumentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', PlatformDashboardController::class)->name('dashboard');
        Route::resource('companies', CompanyController::class)->except('show', 'destroy');
        Route::get('audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    });
    Route::middleware('role:manager,consultant')->prefix('app')->name('app.')->group(function () {
        Route::view('/dashboard', 'app.dashboard')->name('dashboard');
        Route::resource('patients', PatientController::class)->middleware('company.access');
        Route::get('patients/{patient}/documents', [DocumentController::class, 'index'])->middleware(['company.access', 'permission:documents.view'])->name('patients.documents.index');
        Route::get('patients/{patient}/documents/create', [DocumentController::class, 'create'])->middleware(['company.access', 'permission:documents.create'])->name('patients.documents.create');
        Route::post('patients/{patient}/documents', [DocumentController::class, 'store'])->middleware(['company.access', 'permission:documents.create'])->name('patients.documents.store');
        Route::get('documents/{document}/download', [DocumentController::class, 'download'])->middleware('company.access')->name('documents.download');
        Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->middleware(['company.access', 'permission:documents.delete'])->name('documents.destroy');
        Route::resource('consultants', ConsultantController::class)->except(['show', 'destroy'])->middleware('company.access');
        Route::get('consultants/{consultant}/permissions', [ConsultantPermissionController::class, 'edit'])->middleware('role:manager')->name('consultants.permissions.edit');
        Route::put('consultants/{consultant}/permissions', [ConsultantPermissionController::class, 'update'])->middleware('role:manager')->name('consultants.permissions.update');
        Route::get('appointments', [AppointmentController::class, 'index'])->middleware('permission:appointments.view')->name('appointments.index');
        Route::get('appointments/create', [AppointmentController::class, 'create'])->middleware('permission:appointments.create')->name('appointments.create');
        Route::post('appointments', [AppointmentController::class, 'store'])->middleware('permission:appointments.create')->name('appointments.store');
        Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->middleware(['company.access', 'permission:appointments.view'])->name('appointments.show');
        Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->middleware(['company.access', 'permission:appointments.update_status'])->name('appointments.status');
        Route::get('reports/appointments', [ReportController::class, 'appointments'])->middleware('permission:reports.view')->name('reports.appointments');
        Route::get('reports/appointments/export', [ReportController::class, 'exportAppointments'])->middleware('permission:reports.view')->name('reports.appointments.export');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->middleware('permission:audit_logs.view')->name('audit-logs.index');
    });
    Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function () {
        Route::view('/dashboard', 'patient.dashboard')->name('dashboard');
        Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
        Route::get('/documents', [PatientDocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/{document}/download', [PatientDocumentController::class, 'download'])->name('documents.download');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
