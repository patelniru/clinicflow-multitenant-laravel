<?php
namespace App\Models;
use App\Enums\DocumentVisibility;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Document extends Model { use HasFactory,BelongsToCompany; protected $fillable=['company_id','patient_id','uploaded_by','file_name','original_name','file_path','mime_type','file_size','visibility']; protected $casts=['visibility'=>DocumentVisibility::class]; public function patient():BelongsTo{return $this->belongsTo(Patient::class);} public function uploader():BelongsTo{return $this->belongsTo(User::class,'uploaded_by');} }
