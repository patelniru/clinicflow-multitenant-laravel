<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class AppointmentResource extends JsonResource {
 public function toArray(Request $request): array { return ['id'=>$this->id,'patient_id'=>$this->patient_id,'consultant_id'=>$this->consultant_id,'appointment_date'=>$this->appointment_date->format('Y-m-d'),'start_time'=>$this->start_time,'end_time'=>$this->end_time,'status'=>$this->status->value,'notes'=>$this->when($request->user()?->can('view',$this->resource),$this->notes)]; }
}
