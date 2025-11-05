<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use App\Models\Department;
use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GsoDocumentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create GSO department and user
        $this->gsoDepartment = Department::factory()->create(['name' => 'General Services Office']);
        $this->gsoUser = User::factory()->create(['department_id' => $this->gsoDepartment->id]);
        
        // Create document types
        $this->prType = DocumentType::factory()->create(['name' => 'Purchase Request', 'code' => 'PR', 'prefix' => 'PR']);
        $this->poType = DocumentType::factory()->create(['name' => 'Purchase Order', 'code' => 'PO', 'prefix' => 'PO']);
        $this->dvType = DocumentType::factory()->create(['name' => 'Disbursement Voucher', 'code' => 'DV', 'prefix' => 'DV']);
    }

    public function test_gso_can_create_pr_document()
    {
        Storage::fake('public');
        
        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->postJson('/api/documents', [
                'type' => 'PR',
                'file_name' => 'Office Supplies Purchase Request',
                'description' => 'Request for office supplies and materials',
                'priority' => 'medium',
                'security_level' => 'internal',
                'tags' => ['supplies', 'office', 'procurement'],
                'department_id' => $this->gsoDepartment->id,
                'document_type_id' => $this->prType->id
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'document_number',
                    'file_name',
                    'type',
                    'qr_code_path'
                ]
            ]);

        $this->assertDatabaseHas('documents', [
            'file_name' => 'Office Supplies Purchase Request',
            'type' => 'PR',
            'department_id' => $this->gsoDepartment->id,
            'created_by' => $this->gsoUser->id
        ]);

        // Check QR code was generated
        $document = Document::latest()->first();
        $this->assertNotNull($document->qr_code_path);
        Storage::disk('public')->assertExists($document->qr_code_path);
    }

    public function test_gso_can_create_po_document()
    {
        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->postJson('/api/documents', [
                'type' => 'PO',
                'file_name' => 'Purchase Order for IT Equipment',
                'description' => 'Purchase order for computers and peripherals',
                'priority' => 'high',
                'security_level' => 'confidential',
                'department_id' => $this->gsoDepartment->id,
                'document_type_id' => $this->poType->id
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('documents', [
            'file_name' => 'Purchase Order for IT Equipment',
            'type' => 'PO',
            'priority' => 'high',
            'security_level' => 'confidential'
        ]);
    }

    public function test_gso_can_create_dv_document()
    {
        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->postJson('/api/documents', [
                'type' => 'DV',
                'file_name' => 'Disbursement Voucher - Contractor Payment',
                'description' => 'Payment for construction services',
                'priority' => 'urgent',
                'security_level' => 'internal',
                'department_id' => $this->gsoDepartment->id,
                'document_type_id' => $this->dvType->id
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('documents', [
            'file_name' => 'Disbursement Voucher - Contractor Payment',
            'type' => 'DV',
            'priority' => 'urgent'
        ]);
    }

    public function test_gso_can_upload_file_with_document()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('purchase_request.xlsx', 200, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->postJson('/api/documents', [
                'type' => 'PR',
                'file_name' => 'Detailed Purchase Request',
                'description' => 'Purchase request with detailed specifications',
                'priority' => 'medium',
                'department_id' => $this->gsoDepartment->id,
                'document_type_id' => $this->prType->id,
                'file' => $file
            ]);

        $response->assertStatus(201);
        
        $document = Document::latest()->first();
        $this->assertNotNull($document->file_path);
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_gso_can_get_outgoing_documents()
    {
        // Create some GSO documents
        Document::factory()->count(3)->create([
            'department_id' => $this->gsoDepartment->id,
            'created_by' => $this->gsoUser->id,
            'type' => 'PR'
        ]);

        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->getJson('/api/documents/outgoing');

        $response->assertStatus(200);
        
        $documents = $response->json('data');
        $this->assertCount(3, $documents);
        
        foreach ($documents as $doc) {
            $this->assertEquals($this->gsoUser->id, $doc['created_by']);
        }
    }

    public function test_gso_can_get_workflow_status()
    {
        $document = Document::factory()->create([
            'department_id' => $this->gsoDepartment->id,
            'created_by' => $this->gsoUser->id,
            'type' => 'PR',
            'status' => 'received'
        ]);

        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->getJson("/api/workflow/documents/{$document->id}/status");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'current_status',
                    'progress_percentage',
                    'available_actions',
                    'workflow_info'
                ]
            ]);
    }

    public function test_can_track_gso_document()
    {
        $document = Document::factory()->create([
            'document_number' => 'PR-2025-0001',
            'department_id' => $this->gsoDepartment->id,
            'created_by' => $this->gsoUser->id
        ]);

        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->getJson("/api/track/{$document->document_number}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'document_number',
                    'status'
                ]
            ]);
    }

    public function test_gso_can_get_incoming_documents()
    {
        // Create documents sent to GSO department
        Document::factory()->count(2)->create([
            'current_department_id' => $this->gsoDepartment->id,
            'status' => 'submitted',
            'type' => 'PR'
        ]);

        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->getJson('/api/documents/incoming');

        $response->assertStatus(200);
        
        $documents = $response->json('data');
        $this->assertCount(2, $documents);
        
        foreach ($documents as $doc) {
            $this->assertEquals($this->gsoDepartment->id, $doc['current_department_id']);
            $this->assertEquals('submitted', $doc['status']);
        }
    }

    public function test_gso_document_validation()
    {
        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->postJson('/api/documents', [
                'type' => 'INVALID',
                'priority' => 'invalid_priority'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'priority', 'document_type_id', 'department_id']);
    }

    public function test_gso_document_number_generation()
    {
        // Create multiple PR documents to test numbering
        $this->actingAs($this->gsoUser, 'sanctum')
            ->postJson('/api/documents', [
                'type' => 'PR',
                'file_name' => 'First PR',
                'priority' => 'medium',
                'department_id' => $this->gsoDepartment->id,
                'document_type_id' => $this->prType->id
            ]);

        $this->actingAs($this->gsoUser, 'sanctum')
            ->postJson('/api/documents', [
                'type' => 'PR',
                'file_name' => 'Second PR',
                'priority' => 'medium',
                'department_id' => $this->gsoDepartment->id,
                'document_type_id' => $this->prType->id
            ]);

        $documents = Document::where('type', 'PR')->orderBy('created_at')->get();
        
        $this->assertCount(2, $documents);
        $this->assertStringContains('PR-' . date('Y') . '-0001', $documents[0]->document_number);
        $this->assertStringContains('PR-' . date('Y') . '-0002', $documents[1]->document_number);
    }

    public function test_can_forward_gso_document()
    {
        $targetDepartment = Department::factory()->create(['name' => 'Finance Office']);
        $document = Document::factory()->create([
            'department_id' => $this->gsoDepartment->id,
            'current_department_id' => $this->gsoDepartment->id,
            'created_by' => $this->gsoUser->id,
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->patchJson("/api/workflow/documents/{$document->id}/forward", [
                'to_office_id' => $targetDepartment->id,
                'remarks' => 'Please process this request'
            ]);

        $response->assertStatus(200);
        
        $document->refresh();
        $this->assertEquals($targetDepartment->id, $document->current_department_id);
        $this->assertEquals('submitted', $document->status);
    }

    public function test_invalid_document_tracking_returns_404()
    {
        $response = $this->actingAs($this->gsoUser, 'sanctum')
            ->getJson('/api/track/INVALID-DOC-123');

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }
}