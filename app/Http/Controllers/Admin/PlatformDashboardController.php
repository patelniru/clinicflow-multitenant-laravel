<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Appointment; use App\Models\AuditLog; use App\Models\Company; use App\Models\Patient; use App\Models\User;
class PlatformDashboardController extends Controller { public function __invoke(){ $this->authorize('platformView',User::class); return view('admin.dashboard',['companyCount'=>Company::count(),'userCount'=>User::count(),'patientCount'=>Patient::withoutGlobalScopes()->count(),'appointmentCount'=>Appointment::withoutGlobalScopes()->count(),'recentLogs'=>AuditLog::with(['company','user'])->latest()->limit(10)->get()]); } }
