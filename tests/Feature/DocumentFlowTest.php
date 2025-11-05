<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_and_office_assignment(): void
    {
        $this->seed();
        $admin = $this->createUserWithRole('admin');
        $this->actingAs($admin, 'sanctum');

        $dept = Department::factory()->create();
        $res = $this->postJson('/api/users', [
            'name' => 'Jane Admin',
            'email' => 'jane@gerona.gov.ph',
            'password' => 'SecurePass2024!@#',
            'password_confirmation' => 'SecurePass2024!@#',
            'role' => 'admin',
            'department_id' => $dept->id,
        ])->assertCreated();

        $this->assertEquals($dept->id, $res->json('data.department.id'));
    }

    public function test_document_upload_excel_and_qr_generation(): void
    {
        $this->seed();
        $user = $this->createUserWithRole('admin');
        $dept = Department::factory()->create();
        $docType = DocumentType::factory()->create();
        $this->actingAs($user, 'sanctum');

        $doc = $this->postJson('/api/documents', [
            'title' => 'Test Doc',
            'description' => 'desc',
            'type' => 'PR',
            'document_type_id' => $docType->id,
            'department_id' => $dept->id,
            'priority' => 'low',
        ])->assertCreated()->json();

        $file = \Illuminate\Http\UploadedFile::fake()->create('file.xlsx', 10, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->postJson("/api/documents/{$doc['data']['id']}/attachments", [ 'file' => $file ])->assertCreated();

        $this->postJson("/api/documents/{$doc['data']['id']}/qr")->assertOk();
    }

    public function test_forward_receive_reject_workflow(): void
    {
        $this->seed();
        $user = $this->createUserWithRole('admin');
        $deptA = Department::factory()->create();
        $deptB = Department::factory()->create();
        $docType = DocumentType::factory()->create();
        $this->actingAs($user, 'sanctum');

        $doc = $this->postJson('/api/documents', [
            'title' => 'Flow Doc',
            'type' => 'PR',
            'document_type_id' => $docType->id,
            'department_id' => $deptA->id,
            'priority' => 'medium',
        ])->assertCreated()->json();

        $this->postJson("/api/workflow/documents/{$doc['data']['id']}/forward", [
            'to_office_id' => $deptB->id,
            'remarks' => 'forwarded for review',
        ])->assertOk();

        $this->postJson("/api/workflow/documents/{$doc['data']['id']}/receive", [ 'notes' => 'received ok' ])->assertOk();

        $this->postJson("/api/workflow/documents/{$doc['data']['id']}/reject", [ 'reason' => 'incorrect data' ])->assertOk();
    }
}
