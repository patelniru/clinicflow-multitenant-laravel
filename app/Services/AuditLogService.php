<?php
namespace App\Services;
use App\Models\AuditLog;use App\Models\User;use Illuminate\Database\Eloquent\Model;
class AuditLogService { public function record(User $user,string $action,string $module,?Model $model=null,array $metadata=[]):void {AuditLog::create(['company_id'=>$model?->company_id??$user->company_id,'user_id'=>$user->id,'action'=>$action,'module'=>$module,'model_type'=>$model?$model::class:null,'model_id'=>$model?->getKey(),'metadata'=>$metadata,'ip_address'=>request()?->ip()]);} }
