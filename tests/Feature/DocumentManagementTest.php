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

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->department = Department::factory()->create(['name' => 'General Services Office']);
        $this->documentType = DocumentType::factory()->create([
            'name' => 'Purchase Request',
            'code' => 'PR',
            'prefix' => 'PR'
        ]);
        $this->user = $this->createUserWithRole('admin', ['department_id' => $this->department->id]);
    }

    /** @test */
    public function it_can_create_a_document_with_department()
    {
        $user = $this->createUserWithRole('admin');
        $department = Department::factory()->create();
        $documentType = DocumentType::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/documents', [
            'title' => 'Test Document',
            'description' => 'Test Description',
            'department_id' => $department->id,
            'document_type_id' => $documentType->id,
            'type' => 'PR',
            'priority' => 'medium',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'department_id' => $department->id,
        ]);
    }

    public function test_can_create_document_with_qr_code()
    {
        Storage::fake('public');
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documents', [
                'title' => 'Test Document',
                'file_name' => 'Test Document',
                'description' => 'Test description',
                'type' => 'PR',
                'priority' => 'medium',
                'security_level' => 'internal',
                'department_id' => $this->department->id,
                'document_type_id' => $this->documentType->id,
                'tags' => ['test', 'procurement']
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'document_number',
                    'file_name',
                    'type',
                    'status',
                    'qr_code_path'
                ]
            ]);

        $this->assertDatabaseHas('documents', [
            'file_name' => 'Test Document',
            'type' => 'PR',
            'status' => 'draft'
        ]);
    }

    public function test_can_upload_file_with_document()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('test.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documents', [
                'title' => 'Test Excel Document',
                'file_name' => 'Test Excel Document',
                'description' => 'Test Excel upload',
                'type' => 'PR',
                'priority' => 'high',
                'department_id' => $this->department->id,
                'document_type_id' => $this->documentType->id,
                'file' => $file
            ]);

        $response->assertStatus(201);
        
        $document = Document::latest()->first();
        $this->assertNotNull($document->file_path);
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_can_forward_document_to_another_office()
    {
        $targetDepartment = Department::factory()->create(['name' => 'Finance Office']);
        $document = Document::factory()->create([
            'department_id' => $this->department->id,
            'current_department_id' => $this->department->id,
            'created_by' => $this->user->id,
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/workflow/documents/{$document->id}/forward", [
                'to_office_id' => $targetDepartment->id,
                'remarks' => 'Please review and approve'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data'
            ]);

        $this->assertDatabaseHas('document_routes', [
            'document_id' => $document->id,
            'from_office_id' => $this->department->id,
            'to_office_id' => $targetDepartment->id,
            'status' => 'sent',
            'remarks' => 'Please review and approve'
        ]);

        // Check if document's current department was updated
        $document->refresh();
        $this->assertEquals($targetDepartment->id, $document->current_department_id);
        $this->assertEquals('submitted', $document->status->value);
    }

    public function test_can_receive_document()
    {
        $document = Document::factory()->create([
            'current_department_id' => $this->department->id,
            'status' => 'submitted'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/workflow/documents/{$document->id}/receive", [
                'notes' => 'Document received and will be processed'
            ]);

        $response->assertStatus(200);

        $document->refresh();
        $this->assertEquals('received', $document->status->value);
    }

    public function test_can_reject_document()
    {
        $document = Document::factory()->create([
            'current_department_id' => $this->department->id,
            'status' => 'submitted'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/workflow/documents/{$document->id}/reject", [
                'reason' => 'Incomplete documentation'
            ]);

        $response->assertStatus(200);

        $document->refresh();
        $this->assertEquals('rejected', $document->status->value);
    }

    public function test_can_track_document()
    {
        $document = Document::factory()->create([
            'document_number' => 'PR-2025-0001',
            'file_name' => 'Tracked Document'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/track/{$document->document_number}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'document_number',
                    'file_name',
                    'type',
                    'status'
                ]
            ]);
    }

    public function test_can_get_document_history()
    {
        $document = Document::factory()->create([
            'current_department_id' => $this->department->id
        ]);

        // Create some history
        $document->logs()->create([
            'user_id' => $this->user->id,
            'action' => 'created',
            'description' => 'Document created'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/documents/{$document->id}/history");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'routes',
                    'logs' => [
                        '*' => [
                            'id',
                            'action',
                            'description',
                            'created_at',
                            'user'
                        ]
                    ]
                ]
            ]);
    }

    public function test_document_validation_rules()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/documents', [
                // Missing required fields
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'priority', 'document_type_id', 'department_id']);
    }

public function test_can_sign_document()
    {
        $this->markTestSkipped('Sign document functionality needs server-side implementation');
        
        $document = Document::factory()->create([
            'current_department_id' => $this->department->id,
            'status' => 'received'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/workflow/documents/{$document->id}/sign", [
                'remarks' => 'Approved and signed'
            ]);

        $response->assertStatus(200);

        $document->refresh();
        $this->assertEquals('approved', $document->status->value);
    }

    public function test_can_hold_document()
    {
        $document = Document::factory()->create([
            'current_department_id' => $this->department->id,
            'status' => 'received'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/workflow/documents/{$document->id}/hold", [
                'reason' => 'Missing supporting documents',
                'remarks' => 'Please provide additional documentation'
            ]);

        $response->assertStatus(200);

        $document->refresh();
        $this->assertEquals('on_hold', $document->status->value);
        $this->assertEquals('Missing supporting documents', $document->hold_reason);
    }

    public function test_can_resume_document()
    {
        $document = Document::factory()->create([
            'current_department_id' => $this->department->id,
            'status' => 'on_hold',
            'hold_reason' => 'Missing documents'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/workflow/documents/{$document->id}/resume", [
                'remarks' => 'All documents now provided'
            ]);

        $response->assertStatus(200);

        $document->refresh();
        $this->assertEquals('submitted', $document->status->value);
        $this->assertNull($document->hold_reason);
    }

    public function test_can_complete_document()
    {
        $document = Document::factory()->create([
            'current_department_id' => $this->department->id,
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/workflow/documents/{$document->id}/complete", [
                'remarks' => 'All requirements met, document completed'
            ]);

        $response->assertStatus(200);

        $document->refresh();
        $this->assertEquals('completed', $document->status->value);
        $this->assertNotNull($document->completed_at);
    }

    public function test_can_filter_documents()
    {
        // Create test documents
        Document::factory()->create(['type' => 'PR', 'status' => 'draft', 'department_id' => $this->department->id]);
        Document::factory()->create(['type' => 'PO', 'status' => 'approved', 'department_id' => $this->department->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/documents?type=PR&status=draft');

        $response->assertStatus(200);
        
        $documents = $response->json('data');
        $this->assertCount(1, $documents);
        $this->assertEquals('PR', $documents[0]['type']);
        $this->assertEquals('draft', $documents[0]['status']);
    }
}