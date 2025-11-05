<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\User;
use App\Enums\DocumentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_create_document(): void
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;

        $documentData = [
            'title' => 'Test Purchase Request',
            'description' => 'Test Description',
            'document_type_id' => DocumentType::first()->id,
            'department_id' => Department::first()->id,
            'type' => 'PR',
            'priority' => 'high',
            'deadline' => '2025-12-31',
            'file_name' => 'test_document.pdf'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/documents', $documentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'document_number',
                    'title',
                    'description',
                    'document_type',
                    'department',
                    'creator'
                ]
            ]);

        $this->assertDatabaseHas('documents', [
            'title' => 'Test Purchase Request',
            'created_by' => $user->id
        ]);
    }

    public function test_user_can_view_documents(): void
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;

        Document::factory()->create([
            'created_by' => $user->id,
            'status' => DocumentStatus::DRAFT
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/documents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'document_number',
                        'status',
                        'priority',
                        'document_type',
                        'department',
                        'creator'
                    ]
                ]
            ]);
    }

    public function test_user_can_view_single_document(): void
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;

        $document = Document::factory()->create([
            'created_by' => $user->id,
            'status' => DocumentStatus::DRAFT
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/documents/{$document->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'document_number',
                    'status'
                ]
            ])
            ->assertJsonPath('data.id', $document->id)
            ->assertJsonPath('data.title', $document->title);
    }

    public function test_user_can_update_document(): void
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;

        $document = Document::factory()->create([
            'created_by' => $user->id,
            'status' => DocumentStatus::DRAFT
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'status' => 'submitted'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/documents/{$document->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'status'
                ]
            ])
            ->assertJsonPath('data.title', 'Updated Title');

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_user_can_delete_document(): void
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;

        $document = Document::factory()->create([
            'created_by' => $user->id,
            'status' => DocumentStatus::DRAFT
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJsonPath('message', 'Document deleted successfully');

        $this->assertDatabaseMissing('documents', [
            'id' => $document->id
        ]);
    }

    public function test_user_can_upload_attachment(): void
    {
        Storage::fake('public');
        
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $document = Document::factory()->create([
            'created_by' => $user->id,
            'status' => DocumentStatus::DRAFT
        ]);
        
        // Excel only is allowed by controller validation
        $file = UploadedFile::fake()->create('test.xlsx', 1000, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/documents/{$document->id}/attachments", [
            'file' => $file
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'filename',
                    'original_name',
                    'mime_type',
                    'file_size',
                    'uploader'
                ]
            ]);

        $this->assertDatabaseHas('attachments', [
            'document_id' => $document->id,
            'original_name' => 'test.xlsx'
        ]);
    }

    public function test_user_can_track_document(): void
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $document = Document::factory()->create([
            'created_by' => $user->id,
            'status' => DocumentStatus::SUBMITTED
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/track/{$document->document_number}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'document_number',
                    'title',
                    'status',
                    'creator',
                    'logs'
                ]
            ]);
    }

    public function test_document_has_workflow_status(): void
    {
        $user = User::first();
        
        $document = Document::factory()->create([
            'created_by' => $user->id,
            'status' => DocumentStatus::DRAFT
        ]);

        $this->assertEquals(DocumentStatus::DRAFT, $document->status);
        $this->assertTrue($document->canTransitionTo(DocumentStatus::SUBMITTED));
        $this->assertFalse($document->canTransitionTo(DocumentStatus::COMPLETED));
    }
}
