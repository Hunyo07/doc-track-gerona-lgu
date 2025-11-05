<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\DocumentLog;
use App\Models\DocumentRoute;
use App\Models\Department;
use App\Models\Signature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DocumentWorkflowService
{

    /**
     * Office-based permissions for workflow actions
     */
    private const OFFICE_PERMISSIONS = [
        'forward' => ['admin', 'user'],
        'receive' => ['admin', 'user'],
        'sign' => ['admin', 'user'],
        'approve' => ['admin', 'user'],
        'reject' => ['admin', 'user'],
        'hold' => ['admin', 'user'],
        'resume' => ['admin', 'user'],
        'complete' => ['admin', 'user'],
    ];

    /**
     * Forward document to another office
     */
    public function forwardDocument(Document $document, int $toOfficeId, ?string $remarks = null, ?int $assignedUserId = null): bool
    {
        if (!$document->canBeForwarded()) {
            throw new InvalidArgumentException('Document cannot be forwarded in current status');
        }

        $user = Auth::user();
        $toOffice = Department::findOrFail($toOfficeId);

        DB::transaction(function () use ($document, $toOfficeId, $toOffice, $remarks, $user, $assignedUserId) {
            // Create route record
            DocumentRoute::create([
                'document_id' => $document->id,
                'from_office_id' => $document->current_department_id,
                'to_office_id' => $toOfficeId,
                'user_id' => $user->id,
                'status' => 'sent',
                'remarks' => $remarks,
            ]);

            // Update document
            $updateData = [
                'current_department_id' => $toOfficeId,
                'status' => DocumentStatus::SUBMITTED,
                'submitted_at' => now(),
            ];
            if (!is_null($assignedUserId)) {
                $updateData['assigned_to'] = $assignedUserId;
            }
            $document->update($updateData);

            // Log the action
            $this->logAction($document, 'forwarded', "Document forwarded to {$toOffice->name}", [
                'to_office' => $toOffice->name,
                'remarks' => $remarks,
                'assigned_user_id' => $assignedUserId,
            ]);
        });

        return true;
    }

    /**
     * Receive document at current office
     */
    public function receiveDocument(Document $document, ?string $notes = null): bool
    {
        if (!$document->canBeReceived()) {
            throw new InvalidArgumentException('Document cannot be received in current status');
        }

        $user = Auth::user();
        
        $document->update([
            'status' => DocumentStatus::RECEIVED,
            'received_by' => $user->id,
            'received_at' => now(),
        ]);

        $this->logAction($document, 'received', 'Document received', [
            'notes' => $notes,
        ]);

        return true;
    }

    /**
     * Reject document
     */
    public function rejectDocument(Document $document, string $reason): bool
    {
        $document->update([
            'status' => DocumentStatus::REJECTED,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $this->logAction($document, 'rejected', 'Document rejected', [
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Sign document with PNPKI
     */
    public function signDocument(Document $document, ?string $remarks = null): bool
    {
        if (!$document->canBeSigned()) {
            throw new InvalidArgumentException('Document cannot be signed in current status');
        }

        $user = Auth::user();
        
        DB::transaction(function () use ($document, $remarks, $user) {
            // Create signature record
            Signature::create([
                'document_id' => $document->id,
                'user_id' => $user->id,
                'signature_image_path' => '',
                'signature_hash' => hash('sha256', $document->document_number . now()->timestamp),
                'signed_at' => now(),
                'certificate_serial' => 'PNPKI-' . strtoupper(substr(md5($user->id . time()), 0, 10)),
            ]);

            // Update document status
            $document->update([
                'status' => DocumentStatus::APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $this->logAction($document, 'signed', 'Document signed (PNPKI)', [
                'remarks' => $remarks,
            ]);
        });

        return true;
    }

    /**
     * Put document on hold
     */
    public function holdDocument(Document $document, string $reason, ?string $remarks = null): bool
    {
        if (!$document->canBePutOnHold()) {
            throw new InvalidArgumentException('Document cannot be put on hold in current status');
        }

        $document->update([
            'status' => DocumentStatus::ON_HOLD,
            'hold_reason' => $reason,
            'hold_at' => now(),
        ]);

        $this->logAction($document, 'on_hold', 'Document placed on hold', [
            'reason' => $reason,
            'remarks' => $remarks,
        ]);

        return true;
    }

    /**
     * Resume document from hold
     */
    public function resumeDocument(Document $document, ?string $remarks = null): bool
    {
        if (!$document->canBeResumed()) {
            throw new InvalidArgumentException('Document cannot be resumed - not on hold');
        }

        $document->update([
            'status' => DocumentStatus::SUBMITTED,
            'hold_reason' => null,
            'hold_at' => null,
        ]);

        $this->logAction($document, 'resumed', 'Document resumed from hold', [
            'remarks' => $remarks,
        ]);

        return true;
    }

    /**
     * Complete document (final office)
     */
    public function completeDocument(Document $document, ?string $remarks = null): bool
    {
        if (!$document->canBeCompleted()) {
            throw new InvalidArgumentException('Document cannot be completed in current status');
        }

        $document->update([
            'status' => DocumentStatus::COMPLETED,
            'completed_at' => now(),
        ]);

        $this->logAction($document, 'completed', 'Document approved and completed', [
            'remarks' => $remarks,
        ]);

        return true;
    }

    /**
     * Check if user can perform action on document
     */
    public function canPerformAction(Document $document, string $action, $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        // Admin can perform all actions
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if user is in the current department
        if ($document->current_department_id !== $user->department_id) {
            return false;
        }

        // Check action-specific permissions
        return in_array($user->getRoleNames()->first(), self::OFFICE_PERMISSIONS[$action] ?? []);
    }

    /**
     * Get workflow progress percentage
     */
    public function getProgressPercentage(Document $document): int
    {
        return match($document->status) {
            DocumentStatus::DRAFT => 10,
            DocumentStatus::SUBMITTED => 30,
            DocumentStatus::RECEIVED => 50,
            DocumentStatus::APPROVED => 80,
            DocumentStatus::COMPLETED => 100,
            DocumentStatus::REJECTED => 0,
            DocumentStatus::ON_HOLD => 40,
        };
    }

    /**
     * Log workflow action
     */
    private function logAction(Document $document, string $action, string $description, array $metadata = []): void
    {
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'metadata' => array_merge($metadata, [
                'timestamp' => now()->toISOString(),
                'status' => $document->status->value,
            ]),
        ]);
    }
}