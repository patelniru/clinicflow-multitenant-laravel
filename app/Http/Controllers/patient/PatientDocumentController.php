<?php
namespace App\Http\Controllers\Patient;
use App\Http\Controllers\Controller;use App\Models\Document;use Illuminate\Http\Request;use Illuminate\Support\Facades\Storage;
class PatientDocumentController extends Controller { public function index(Request $request){$patient=$request->user()->patientProfile;abort_unless($patient,403);$documents=Document::query()->where('patient_id',$patient->id)->where('visibility','patient_visible')->latest()->paginate(15);return view('patient.documents.index',compact('documents'));} public function download(Request $request,Document $document){$this->authorize('view',$document);abort_unless(Storage::disk('private')->exists($document->file_path),404);return Storage::disk('private')->download($document->file_path,$document->original_name);} }
