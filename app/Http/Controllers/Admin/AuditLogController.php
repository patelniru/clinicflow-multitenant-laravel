<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\AuditLog;use App\Models\Company;use Illuminate\Http\Request;
class AuditLogController extends Controller { public function index(Request $request){abort_unless($request->user()->isSuperAdmin(),403);$logs=AuditLog::withoutGlobalScopes()->with(['company','user'])->when($request->company_id,fn($q,$id)=>$q->where('company_id',$id))->when($request->module,fn($q,$m)=>$q->where('module',$m))->latest()->simplePaginate(25)->withQueryString();$companies=Company::orderBy('name')->paginate(15);$modules=AuditLog::withoutGlobalScopes()->select('module')->distinct()->orderBy('module')->pluck('module');return view('admin.audit-logs.index',compact('logs','companies','modules'));} }
