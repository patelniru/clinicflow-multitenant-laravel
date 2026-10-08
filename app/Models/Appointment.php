<?php
namespace App\Models;
use App\Enums\AppointmentStatus;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Appointment extends Model {
    use BelongsToCompany,HasFactory;
    protected $fillable=['company_id','patient_id','consultant_id','appointment_date','start_time','end_time','status','notes','created_by'];
    protected $casts=['appointment_date'=>'date','status'=>AppointmentStatus::class];
    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function consultant(): BelongsTo { return $this->belongsTo(ConsultantProfile::class,'consultant_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class,'created_by'); }
}
