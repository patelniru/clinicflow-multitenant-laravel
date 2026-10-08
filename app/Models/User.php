<?php
namespace App\Models;
use App\Enums\UserRole;
use App\Models\Concerns\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class User extends Authenticatable { use HasFactory,Notifiable,HasPermissions; protected $fillable=['company_id','name','email','password','role','is_active']; protected $hidden=['password','remember_token']; protected $casts=['password'=>'hashed','role'=>UserRole::class,'is_active'=>'boolean']; public function company(): BelongsTo{return $this->belongsTo(Company::class);} public function patientProfile(): HasOne{return $this->hasOne(Patient::class);} public function consultantProfile(): HasOne{return $this->hasOne(ConsultantProfile::class);} public function isSuperAdmin(): bool{return $this->role===UserRole::SUPER_ADMIN;} public function hasAnyRole(UserRole ...$roles): bool{return in_array($this->role,$roles,true);} }
