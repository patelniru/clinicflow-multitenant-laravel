<?php
namespace App\Http\Controllers\Api\V1;
use App\Actions\Appointments\CreateAppointmentAction;
use App\Contracts\AppointmentRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\Request;
class AppointmentController extends Controller {
    public function __construct(private AppointmentRepositoryInterface $appointments) {}
    public function index(Request $request) { return AppointmentResource::collection($this->appointments->paginate($request->only('status','date'))); }
    public function store(StoreAppointmentRequest $request, CreateAppointmentAction $action) {
        $data=$request->validated(); $data['company_id']=$request->user()->company_id;
        $appointment=$action->execute($data,$request->user()->id);
        return (new AppointmentResource($appointment))->response()->setStatusCode(201);
    }
    public function show(Appointment $appointment) { $this->authorize('view',$appointment); return new AppointmentResource($appointment); }
}
