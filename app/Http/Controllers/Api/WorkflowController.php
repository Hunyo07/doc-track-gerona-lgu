<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Document;
use App\Services\DocumentWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
    public function __construct(
        private DocumentWorkflowService $workflowService
    ) {}

    /**
     * Forward document to another office
     */
    public function forward(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        
        $request->validate([
            'to_office_id' => 'required|exists:departments,id',
            'remarks' => 'nullable|string|max:1000',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        try {
            $this->workflowService->forwardDocument(
                $document,
                $request->to_office_id,
                $request->remarks,
                $request->assigned_user_id
            );

            return ApiResponse::success(
                $document->fresh(['currentDepartment', 'routes.toOffice']),
                'Document forwarded successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error forwarding document: ' . $e->getMessage());
            return ApiResponse::serverError('Failed to forward document: ' . $e->getMessage());
        }
    }

    /**
     * Receive document at current office
     */
    public function receive(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $this->workflowService->receiveDocument($document, $request->notes);

            return ApiResponse::success(
                $document->fresh(['receivedBy']),
                'Document received successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error receiving document: ' . $e->getMessage());
            return ApiResponse::serverError('Failed to receive document: ' . $e->getMessage());
        }
    }

    /**
     * Reject document
     */
    public function reject(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        try {
            $this->workflowService->rejectDocument($document, $request->reason);

            return ApiResponse::success(
                $document->fresh(['rejectedBy']),
                'Document rejected successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error rejecting document: ' . $e->getMessage());
            return ApiResponse::serverError('Failed to reject document: ' . $e->getMessage());
        }
    }

    /**
     * Sign document with PNPKI
     */
    public function sign(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        
        $request->validate([
            'remarks' => 'nullable|string|max:1000'
        ]);

        try {
            $this->workflowService->signDocument($document, $request->remarks);

            return ApiResponse::success(
                $document->fresh(['signatures', 'approvedBy']),
                'Document signed successfully'
            );
        } catch (\InvalidArgumentException $e) {
            // Invalid state for signing; return 422 to avoid generic 500
            return ApiResponse::validationError([
                'status' => [$e->getMessage()],
            ], 'Document cannot be signed');
        } catch (\Exception $e) {
            Log::error('Error signing document: ' . $e->getMessage());
            return ApiResponse::serverError('Failed to sign document: ' . $e->getMessage());
        }
    }

    /**
     * Put document on hold
     */
    public function hold(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        
        $request->validate([
            'reason' => 'required|string|max:1000',
            'remarks' => 'nullable|string|max:1000'
        ]);

        try {
            $this->workflowService->holdDocument($document, $request->reason, $request->remarks);

            return ApiResponse::success(
                $document->fresh(),
                'Document placed on hold successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error holding document: ' . $e->getMessage());
            return ApiResponse::serverError('Failed to hold document: ' . $e->getMessage());
        }
    }

    /**
     * Resume document from hold
     */
    public function resume(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        
        $request->validate([
            'remarks' => 'nullable|string|max:1000'
        ]);

        try {
            $this->workflowService->resumeDocument($document, $request->remarks);

            return ApiResponse::success(
                $document->fresh(),
                'Document resumed successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error resuming document: ' . $e->getMessage());
            return ApiResponse::serverError('Failed to resume document: ' . $e->getMessage());
        }
    }

    /**
     * Complete document (final office)
     */
    public function complete(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        
        $request->validate([
            'remarks' => 'nullable|string|max:1000'
        ]);

        try {
            $this->workflowService->completeDocument($document, $request->remarks);

            return ApiResponse::success(
                $document->fresh(),
                'Document completed successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error completing document: ' . $e->getMessage());
            return ApiResponse::serverError('Failed to complete document: ' . $e->getMessage());
        }
    }

    /**
     * Get document workflow status and available actions
     */
    public function status(Request $request, Document $document)
    {
        $this->authorize('view', $document);

        $user = $request->user();
        
        return ApiResponse::success([
            'current_status' => $document->status,
            'progress_percentage' => $this->workflowService->getProgressPercentage($document),
            'available_actions' => [
                'can_forward' => $document->canBeForwarded() && $this->workflowService->canPerformAction($document, 'forward', $user),
                'can_receive' => $document->canBeReceived() && $this->workflowService->canPerformAction($document, 'receive', $user),
                'can_sign' => $document->canBeSigned() && $this->workflowService->canPerformAction($document, 'sign', $user),
                'can_reject' => $this->workflowService->canPerformAction($document, 'reject', $user),
                'can_hold' => $document->canBePutOnHold() && $this->workflowService->canPerformAction($document, 'hold', $user),
                'can_resume' => $document->canBeResumed() && $this->workflowService->canPerformAction($document, 'resume', $user),
                'can_complete' => $document->canBeCompleted() && $this->workflowService->canPerformAction($document, 'complete', $user),
            ],
            'workflow_info' => [
                'current_office' => $document->currentDepartment?->name,
                'received_by' => $document->receivedBy?->name,
                'received_at' => $document->received_at,
                'hold_reason' => $document->hold_reason,
                'hold_at' => $document->hold_at,
            ]
        ], 'Document workflow status retrieved successfully');
    }
}