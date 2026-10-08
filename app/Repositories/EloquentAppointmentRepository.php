<?php
namespace App\Repositories;
use App\Contracts\AppointmentRepositoryInterface;
use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class EloquentAppointmentRepository implements AppointmentRepositoryInterface {
    public function paginate(array $filters=[]): LengthAwarePaginator {
        return Appointment::query()->with(['patient','consultant.user'])
            ->when($filters['status'] ?? null, fn($q,$v)=>$q->where('status',$v))
            ->when($filters['date'] ?? null, fn($q,$v)=>$q->whereDate('appointment_date',$v))
            ->orderBy('appointment_date')->orderBy('start_time')->paginate(15)->withQueryString();
    }
    public function create(array $data): Appointment { return Appointment::create($data); }
    public function findOrFail(int $id): Appointment { return Appointment::findOrFail($id); }
}
