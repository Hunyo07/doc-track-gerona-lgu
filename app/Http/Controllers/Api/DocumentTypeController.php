<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\DocumentType;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
{
    /**
     * List document types (cached).
     */
    public function index()
    {
        $documentTypes = CacheService::getCachedDocumentTypes();
        if (!$documentTypes) {
            $documentTypes = DocumentType::all();
            CacheService::cacheDocumentTypes($documentTypes);
        }
        return response()->json($documentTypes);
    }

    /**
     * Create a new document type.
     */
    public function store(Request $request)
    {
        // Admin-only
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('document_types', 'code')],
            'description' => ['nullable', 'string', 'max:1000'],
            'prefix' => ['nullable', 'string', 'max:50'],
            'requires_approval' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'schema' => ['nullable', 'array'],
        ]);

        $type = DocumentType::create($validated);
        CacheService::cacheDocumentTypes(DocumentType::all());

        return ApiResponse::success($type, 'Document type created', 201);
    }

    /**
     * Update a document type.
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('document_types', 'code')->ignore($documentType->id)],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'prefix' => ['sometimes', 'nullable', 'string', 'max:50'],
            'requires_approval' => ['sometimes', 'nullable', 'boolean'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
            'schema' => ['sometimes', 'nullable', 'array'],
        ]);

        $documentType->update($validated);
        CacheService::cacheDocumentTypes(DocumentType::all());

        return ApiResponse::success($documentType->fresh(), 'Document type updated');
    }

    /**
     * Delete a document type.
     */
    public function destroy(Request $request, DocumentType $documentType)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $documentType->delete();
        CacheService::cacheDocumentTypes(DocumentType::all());

        return ApiResponse::success(null, 'Document type deleted');
    }
}