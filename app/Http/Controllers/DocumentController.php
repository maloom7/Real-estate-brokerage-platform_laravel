<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyDocument;
use App\Http\Requests\UploadDocumentRequest;
use App\Services\DocumentService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
        $this->middleware(['auth']);
    }

    public function upload(UploadDocumentRequest $request, Property $property)
    {
        $this->authorize('upload', $property);

        $result = $this->documentService->upload(
            $request->file('document'),
            "properties/{$property->id}/documents",
            $request->is_confidential
        );

        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        $document = $property->documents()->create([
            'document_type_id' => $request->document_type_id,
            'file_path' => $result['path'],
            'file_name' => $result['original_name'],
            'mime_type' => $result['mime_type'],
            'file_size' => $result['size'],
            'is_confidential' => $result['is_confidential'],
            'verification_status' => 'pending'
        ]);

        activity()
            ->performedOn($document)
            ->withProperties(['property_id' => $property->id])
            ->log('uploaded_document');

        return back()->with('success', 'تم رفع المستند بنجاح');
    }

    public function download(PropertyDocument $document)
    {
        $this->authorize('view', $document);

        activity()
            ->performedOn($document)
            ->withProperties(['action' => 'download', 'user_id' => auth()->id()])
            ->log('downloaded_document');

        return $this->documentService->download(
            $document->file_path,
            $document->file_name,
            $document->is_confidential
        );
    }

    public function verify(Request $request, PropertyDocument $document)
    {
        $this->authorize('verify', $document);

        $request->validate([
            'status' => 'required|in:verified,rejected'
        ]);

        $document->update([
            'verification_status' => $request->status,
            'verified_by' => auth()->id()
        ]);

        activity()
            ->performedOn($document)
            ->withProperties(['status' => $request->status])
            ->log('verified_document');

        return back()->with('success', 'تم تحديث حالة المستند');
    }

    public function destroy(PropertyDocument $document)
    {
        $this->authorize('delete', $document);

        $this->documentService->delete(
            $document->file_path,
            $document->is_confidential
        );

        $document->delete();

        activity()
            ->performedOn($document)
            ->log('deleted_document');

        return back()->with('success', 'تم حذف المستند');
    }
}