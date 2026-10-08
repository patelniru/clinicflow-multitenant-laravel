<?php
namespace App\Contracts;
use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
interface AppointmentRepositoryInterface {
    public function paginate(array $filters=[]): LengthAwarePaginator;
    public function create(array $data): Appointment;
    public function findOrFail(int $id): Appointment;
}
