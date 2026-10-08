<?php

namespace App\Http\Controllers\App;

use App\Enums\DocumentVisibility;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Patient;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Patient $patient)
    {
        $this->authorize('view', $patient);
        $documents = Document::query()->where('patient_id', $patient->id)->latest()->paginate(15);
        return view('app.documents.index', compact('patient', 'documents'));
    }
    public function create(Patient $patient)
    {
        $this->authorize('create', Document::class);
        $this->authorize('view', $patient);
        return view('app.documents.create', compact('patient'));
    }
    public function store(Request $request, Patient $patient, AuditLogService $audit)
    {
        $this->authorize('create', Document::class);
        $this->authorize('view', $patient);
        $data = $request->validate(['document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:5120'], 'visibility' => ['required', Rule::in(array_map(fn($v) => $v->value, DocumentVisibility::cases()))]]);
        $file = $data['document'];
        $path = $file->store("documents/{$patient->company_id}/{$patient->id}", 'private');
        $document = Document::create(['company_id' => $patient->company_id, 'patient_id' => $patient->id, 'uploaded_by' => $request->user()->id, 'file_name' => basename($path), 'original_name' => $file->getClientOriginalName(), 'file_path' => $path, 'mime_type' => $file->getMimeType(), 'file_size' => $file->getSize(), 'visibility' => $data['visibility']]);
        $audit->record($request->user(), 'document.uploaded', 'documents', $document, ['name' => $document->original_name]);
        return redirect()->route('app.patients.documents.index', $patient)->with('success', 'Document uploaded.');
    }
    public function download(Document $document, Request $request, AuditLogService $audit)
    {
        $this->authorize('view', $document);
        abort_unless(Storage::disk('private')->exists($document->file_path), 404);
        $audit->record($request->user(), 'document.downloaded', 'documents', $document, ['name' => $document->original_name]);
        return Storage::disk('private')->download($document->file_path, $document->original_name);
    }
    public function destroy(Document $document, Request $request, AuditLogService $audit)
    {
        $this->authorize('delete', $document);
        Storage::disk('private')->delete($document->file_path);
        $audit->record($request->user(), 'document.deleted', 'documents', $document, ['name' => $document->original_name]);
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }
}
