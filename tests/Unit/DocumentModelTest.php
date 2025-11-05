<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Models\User;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\DocumentLog;
use App\Models\DocumentRoute;
use App\Models\QRCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_has_fillable_attributes()
    {
        $fillable = [
            'document_number',
            'file_name',
            'description',
            'tags',
            'sender_id',
            'type',
            'security_level',
            'priority',
            'status',
            'qr_code_path',
            'file_path',
            'department_id',
            'document_type_id',
            'current_department_id',
            'created_by',
            'barcode',
            'title',
            'submission_date',
            'deadline',
            'metadata',
            'submitted_at',
            'received_at',
            'approved_at',
            'rejected_at',
            'completed_at',
            'archived_at',
            'approved_by',
            'rejected_by',
            'rejection_reason',
            'assigned_to',
            'received_by',
            'hold_reason',
            'hold_at',
        ];

        $document = new Document();
        $this->assertEquals($fillable, $document->getFillable());
    }

    public function test_document_casts_attributes_correctly()
    {
        $document = new Document();
        $casts = $document->getCasts();

        $this->assertEquals('date', $casts['submission_date']);
        $this->assertEquals('date', $casts['deadline']);
        $this->assertEquals('array', $casts['metadata']);
        $this->assertEquals('array', $casts['tags']);
    }

    public function test_document_belongs_to_document_type()
    {
        $documentType = DocumentType::factory()->create();
        $document = Document::factory()->create(['document_type_id' => $documentType->id]);

        $this->assertInstanceOf(DocumentType::class, $document->documentType);
        $this->assertEquals($documentType->id, $document->documentType->id);
    }

    public function test_document_belongs_to_department()
    {
        $department = Department::factory()->create();
        $document = Document::factory()->create(['department_id' => $department->id]);

        $this->assertInstanceOf(Department::class, $document->department);
        $this->assertEquals($department->id, $document->department->id);
    }

    public function test_document_belongs_to_current_department()
    {
        $department = Department::factory()->create();
        $document = Document::factory()->create(['current_department_id' => $department->id]);

        $this->assertInstanceOf(Department::class, $document->currentDepartment);
        $this->assertEquals($department->id, $document->currentDepartment->id);
    }

    public function test_document_belongs_to_creator()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $document->creator);
        $this->assertEquals($user->id, $document->creator->id);
    }

    public function test_document_belongs_to_sender()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['sender_id' => $user->id]);

        $this->assertInstanceOf(User::class, $document->sender);
        $this->assertEquals($user->id, $document->sender->id);
    }

    public function test_document_has_many_logs()
    {
        $document = Document::factory()->create();
        $log1 = DocumentLog::factory()->create(['document_id' => $document->id]);
        $log2 = DocumentLog::factory()->create(['document_id' => $document->id]);

        $this->assertCount(2, $document->logs);
        $this->assertTrue($document->logs->contains($log1));
        $this->assertTrue($document->logs->contains($log2));
    }

    public function test_document_has_many_routes()
    {
        $document = Document::factory()->create();
        $department1 = Department::factory()->create();
        $department2 = Department::factory()->create();
        $user = User::factory()->create();

        $route1 = DocumentRoute::factory()->create([
            'document_id' => $document->id,
            'from_office_id' => $department1->id,
            'to_office_id' => $department2->id,
            'user_id' => $user->id
        ]);

        $this->assertCount(1, $document->routes);
        $this->assertTrue($document->routes->contains($route1));
    }

    public function test_document_has_one_qr_code()
    {
        $document = Document::factory()->create();
        $qrCode = QRCode::factory()->create(['document_id' => $document->id]);

        $this->assertInstanceOf(QRCode::class, $document->qrCode);
        $this->assertEquals($qrCode->id, $document->qrCode->id);
    }

    public function test_document_can_store_tags_as_array()
    {
        $tags = ['procurement', 'urgent', 'supplies'];
        $document = Document::factory()->create(['tags' => $tags]);

        $this->assertEquals($tags, $document->tags);
        $this->assertIsArray($document->tags);
    }

    public function test_document_can_store_metadata_as_array()
    {
        $metadata = [
            'cloud_share_token' => 'abc123',
            'special_instructions' => 'Handle with care',
            'budget_code' => 'BUD-2025-001'
        ];
        $document = Document::factory()->create(['metadata' => $metadata]);

        $this->assertEquals($metadata, $document->metadata);
        $this->assertIsArray($document->metadata);
    }

    public function test_document_status_enum_values()
    {
        $validStatuses = [
            'draft', 'submitted', 'received', 'approved', 'rejected', 'completed', 'on_hold'
        ];

        foreach ($validStatuses as $status) {
            $document = Document::factory()->create(['status' => $status]);
            $this->assertEquals($status, $document->status->value);
        }
    }

    public function test_document_type_enum_values()
    {
        $validTypes = ['PR', 'PO', 'DV', 'bid', 'award', 'contract', 'other'];

        foreach ($validTypes as $type) {
            $document = Document::factory()->create(['type' => $type]);
            $this->assertEquals($type, $document->type);
        }
    }

    public function test_document_priority_enum_values()
    {
        $validPriorities = ['low', 'medium', 'high', 'urgent'];

        foreach ($validPriorities as $priority) {
            $document = Document::factory()->create(['priority' => $priority]);
            $this->assertEquals($priority, $document->priority);
        }
    }

    public function test_document_security_level_values()
    {
        $validSecurityLevels = ['public', 'internal', 'confidential', 'secret'];

        foreach ($validSecurityLevels as $level) {
            $document = Document::factory()->create(['security_level' => $level]);
            $this->assertEquals($level, $document->security_level);
        }
    }
}