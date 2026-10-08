<?php
namespace App\Models;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ConsultantProfile extends Model {
    use BelongsToCompany, HasFactory;
    protected $fillable=['company_id','user_id','specialty','is_active'];
    protected $casts=['is_active'=>'boolean'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function appointments(): HasMany { return $this->hasMany(Appointment::class,'consultant_id'); }
}
