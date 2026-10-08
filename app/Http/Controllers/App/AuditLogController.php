<?php
namespace App\Http\Controllers\App;
use App\Http\Controllers\Controller;use App\Models\AuditLog;use Illuminate\Http\Request;
class AuditLogController extends Controller { public function index(Request $request){abort_unless($request->user()->hasPermission('audit_logs.view'),403);$logs=AuditLog::query()->with('user')->when($request->module,fn($q,$m)=>$q->where('module',$m))->when($request->action,fn($q,$a)=>$q->where('action','like',"%{$a}%"))->latest()->simplePaginate(25)->withQueryString();$modules=AuditLog::query()->select('module')->distinct()->orderBy('module')->pluck('module');return view('app.audit-logs.index',compact('logs','modules'));} }
