<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::guard('supplier')->user();
        $supplier = $user->supplier;
        $approval = $supplier->approvement;
        $status = $approval->action ?? 'pending';

        // Get current approval documents
        $currentDocuments = $supplier->getMedia('approval_documents');

        // Get previous rejected documents if exists
        $previousDocuments = collect();
        if ($status === 'rejected') {
            $previousDocuments = $supplier->getMedia('approval_documents_rejected');
        }

        return view('backend.dashboards.supplier.approval.show', compact(
            'supplier',
            'approval',
            'status',
            'currentDocuments',
            'previousDocuments'
        ));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::guard('supplier')->user();
            $supplier = $user->supplier;
            $approval = $supplier->approvement;

            // If status was rejected, move current documents to rejected collection
            if ($approval->action === 'rejected') {
                $currentDocs = $supplier->getMedia('approval_documents');
                foreach ($currentDocs as $doc) {
                    $doc->move($supplier, 'approval_documents_rejected');
                }
            }

            // Clear current approval documents
            $supplier->clearMediaCollection('approval_documents');

            // Upload new documents
            foreach ($request->file('documents') as $document) {
                $supplier->addMedia($document)
                    ->toMediaCollection('approval_documents');
            }

            // Update approval status to under_review
            $approval->update([
                'action' => 'under_review',
                'notes' => 'Documents uploaded for review'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully! Your supplier account is now under review.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload documents. Please try again.'
            ], 500);
        }
    }
}
