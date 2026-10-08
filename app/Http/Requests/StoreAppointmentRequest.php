<?php
namespace App\Http\Requests;
use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreAppointmentRequest extends FormRequest {
    public function authorize(): bool { return $this->user()->hasAnyRole(\App\Enums\UserRole::SUPER_ADMIN,\App\Enums\UserRole::MANAGER,\App\Enums\UserRole::CONSULTANT); }
    public function rules(): array { return [
        'patient_id'=>['required','integer','exists:patients,id'], 'consultant_id'=>['required','integer','exists:consultant_profiles,id'],
        'appointment_date'=>['required','date','after_or_equal:today'], 'start_time'=>['required','date_format:H:i'], 'end_time'=>['required','date_format:H:i','after:start_time'],
        'status'=>['sometimes',Rule::enum(AppointmentStatus::class)], 'notes'=>['nullable','string','max:2000']
    ]; }
}
