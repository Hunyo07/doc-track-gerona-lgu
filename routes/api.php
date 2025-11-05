<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\QRCodeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DocumentTrackingController;
use App\Http\Controllers\Api\SignatureController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\GsoDocumentController;
use App\Http\Controllers\IssueReportController;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes with rate limiting
Route::prefix('auth')->middleware('rate.limit')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
});

// Public routes with rate limiting (no authentication required)
Route::middleware('rate.limit')->group(function () {
    // Department routes (public access for listing departments)
    Route::get('departments', [DepartmentController::class, 'index']);
});

// Protected routes with rate limiting
Route::middleware(['auth:sanctum', 'rate.limit'])->group(function () {
    // Dashboard stats
    Route::get('dashboard/stats', [DocumentTrackingController::class, 'getDashboardStats']);
    // Document routes without access control (index, create, store)
    Route::post('documents/generate-qr', [DocumentController::class, 'generateQR']);
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    
    // Document routes with DocumentPolicy authorization (handled by authorizeResource in DocumentController)
    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show')->whereNumber('document');
    Route::get('documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit')->whereNumber('document');
    Route::put('documents/{document}', [DocumentController::class, 'update'])->name('documents.update')->whereNumber('document');
    Route::patch('documents/{document}', [DocumentController::class, 'update'])->whereNumber('document');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy')->whereNumber('document');
    Route::get('documents/{document}/history', [DocumentController::class, 'getHistory'])->whereNumber('document');
    Route::post('documents/{document}/route', [DocumentController::class, 'routeDocument'])->whereNumber('document');
    
    // Workflow routes - new dedicated endpoints
    Route::prefix('workflow')->group(function () {
        Route::post('documents/{document}/forward', [WorkflowController::class, 'forward'])->whereNumber('document');
        Route::post('documents/{document}/receive', [WorkflowController::class, 'receive'])->whereNumber('document');
        Route::post('documents/{document}/reject', [WorkflowController::class, 'reject'])->whereNumber('document');
        Route::post('documents/{document}/sign', [WorkflowController::class, 'sign'])->whereNumber('document');
        Route::post('documents/{document}/hold', [WorkflowController::class, 'hold'])->whereNumber('document');
        Route::post('documents/{document}/resume', [WorkflowController::class, 'resume'])->whereNumber('document');
        Route::post('documents/{document}/complete', [WorkflowController::class, 'complete'])->whereNumber('document');
        Route::get('documents/{document}/status', [WorkflowController::class, 'status'])->whereNumber('document');
    });
    
    // Document routes that need additional middleware protection
    Route::middleware('document.access')->group(function () {
        Route::post('documents/{document}/attachments', [AttachmentController::class, 'store'])->whereNumber('document');
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);
    });
    
    // Document management routes
    Route::post('documents/bulk-forward', [DocumentController::class, 'bulkForward']);
    Route::post('documents/bulk-update-status', [DocumentController::class, 'bulkUpdateStatus']);
    Route::post('documents/bulk-update-department', [DocumentController::class, 'bulkUpdateDepartment']);
    Route::post('documents/bulk-assign', [DocumentController::class, 'bulkAssign']);
    Route::delete('documents/bulk-delete', [DocumentController::class, 'bulkDelete']);
    Route::get('documents-export', [DocumentController::class, 'export']);
    Route::get('documents-stats', [DocumentController::class, 'getStats']);
    Route::post('documents/find-by-barcode', [DocumentController::class, 'findByBarcode']);
    
    // Page-specific document endpoints
    // Route::get('documents/outgoing', [DocumentController::class, 'getOutgoing']);
    // Route::get('documents/incoming', [DocumentController::class, 'getIncoming']);
    // Route::get('documents/received', [DocumentController::class, 'getReceived']);
    // Route::get('documents/hold', [DocumentController::class, 'getOnHold']);
    // Route::get('documents/completed', [DocumentController::class, 'getCompleted']);
    Route::prefix('documents')->group(function () {
  Route::get('/incoming', [DocumentController::class, 'getIncoming']);
    Route::get('/outgoing', [DocumentController::class, 'getOutgoing']);
    Route::get('/received', [DocumentController::class, 'getReceived']);
    Route::get('/hold', [DocumentController::class, 'getOnHold']);
    Route::get('/completed', [DocumentController::class, 'getCompleted']);

    // Other non-dynamic routes
    Route::get('/stats', [DocumentController::class, 'getStats']);
    Route::post('/bulk-update-status', [DocumentController::class, 'bulkUpdateStatus']);
    Route::post('/bulk-forward', [DocumentController::class, 'bulkForward']);
    // ... etc ...

    // 🔴 The dynamic catch-all must always go last (numeric ids only):
    Route::get('/{document}', [DocumentController::class, 'show'])->whereNumber('document');
    Route::put('/{document}', [DocumentController::class, 'update'])->whereNumber('document');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->whereNumber('document');
});

    // File download routes with access control
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->middleware('file.access');
    
    // QR Tracker endpoint
    Route::get('track/{identifier}', [DocumentController::class, 'trackDocument']);
    Route::post('track/scan', [DocumentController::class, 'scanQRCode']);
    
    // QR Code routes
    Route::post('documents/{document}/qr', [QRCodeController::class, 'generate'])->whereNumber('document');
    Route::post('qr/generate', [QRCodeController::class, 'generateGeneric']);
    Route::post('qr/import', [QRCodeController::class, 'import']);
    Route::post('qr-codes/{document}/scan', [QRCodeController::class, 'logScan'])->whereNumber('document');
    Route::get('qr-codes/{document}/view', [QRCodeController::class, 'viewDocument'])->whereNumber('document');
    
    // User management routes
    Route::apiResource('users', UserController::class);
    Route::put('users/{user}/password', [UserController::class, 'updatePassword']);
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
    Route::delete('users/{user}/remove-role', [UserController::class, 'removeRole']);
    Route::post('users/{user}/assign-permission', [UserController::class, 'assignPermission']);
    Route::delete('users/{user}/remove-permission', [UserController::class, 'removePermission']);
    Route::get('roles', [UserController::class, 'getRoles']);
    Route::get('permissions', [UserController::class, 'getPermissions']);
    
    // Signatures routes
    Route::get('signatures', [SignatureController::class, 'index']);
    
    // Department routes (management - requires authentication)
    Route::post('departments', [DepartmentController::class, 'store']);
    Route::put('departments/{department}', [DepartmentController::class, 'update']);
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy']);
    
    // GSO Document routes
    Route::prefix('gso')->group(function () {
        Route::post('documents', [GsoDocumentController::class, 'create']);
        Route::get('documents/recent', [GsoDocumentController::class, 'getRecentDocuments']);
        Route::post('documents/bulk-upload', [GsoDocumentController::class, 'bulkUpload']);
        Route::post('documents/{document}/cloud-link', [GsoDocumentController::class, 'generateCloudLink'])->whereNumber('document');
    });
    
    // Issue Report routes
    Route::prefix('issues')->group(function () {
        Route::get('/', [IssueReportController::class, 'index']);
        Route::post('/', [IssueReportController::class, 'store']);
        Route::get('/{issue}', [IssueReportController::class, 'show']);
        Route::put('/{issue}', [IssueReportController::class, 'update']);
        Route::delete('/{issue}', [IssueReportController::class, 'destroy']);
        Route::get('/statistics/overview', [IssueReportController::class, 'statistics']);
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Audit log routes
    Route::prefix('audit-logs')->group(function () {
        Route::get('/', [AuditLogController::class, 'index']);
        Route::get('/security', [AuditLogController::class, 'security']);
        Route::get('/authentication', [AuditLogController::class, 'authentication']);
        Route::get('/stats', [AuditLogController::class, 'stats']);
        Route::get('/documents/{document}', [AuditLogController::class, 'document'])->whereNumber('document');
        Route::get('/users/{user}', [AuditLogController::class, 'user'])->whereNumber('user');
        Route::get('/export', [AuditLogController::class, 'export']);
    });
});

// Public QR view route with rate limiting
Route::get('qr/{token}', [QRCodeController::class, 'view'])->name('qr.view')->middleware('rate.limit');

// Public document share route with rate limiting
Route::get('documents/share/{documentNumber}', [DocumentController::class, 'shareDocument'])->name('document.share')->middleware('rate.limit');

// Public cloud access route with rate limiting
Route::get('documents/cloud/{token}', [GsoDocumentController::class, 'accessViaCloudToken'])->name('document.cloud')->middleware('rate.limit');

// Document types endpoint with caching
Route::get('document-types', function () {
    // Try to get from cache first
    $documentTypes = \App\Services\CacheService::getCachedDocumentTypes();
    
    if (!$documentTypes) {
        // If not in cache, get from database and cache it
        $documentTypes = \App\Models\DocumentType::all();
        \App\Services\CacheService::cacheDocumentTypes($documentTypes);
    }
    
    return response()->json($documentTypes);
})->middleware('rate.limit');

// Debug endpoint to check database setup
Route::get('debug/database', function () {
    return response()->json([
        'document_types' => \App\Models\DocumentType::all(),
        'documents' => \App\Models\Document::all(),
        'departments' => \App\Models\Department::all(),
        'users_count' => \App\Models\User::count(),
    ]);
    try {
        $debug = [
            'tables' => [],
            'documents_columns' => [],
            'data_counts' => [],
            'sample_data' => []
        ];

        // ✅ List of tables to check
        $tables = ['documents', 'document_types', 'departments', 'users', 'qr_codes'];

        foreach ($tables as $table) {
            $debug['tables'][$table] = Schema::hasTable($table);
        }

        // ✅ Get document table columns if it exists
        if (Schema::hasTable('documents')) {
            $debug['documents_columns'] = Schema::getColumnListing('documents');

            // Include all documents (or limit to avoid huge data)
            $debug['sample_data']['documents'] = Document::limit(50)->get(); 
        }

        // ✅ Count records for each table
        if (Schema::hasTable('document_types')) {
            $debug['data_counts']['document_types'] = DocumentType::count();
            $debug['sample_data']['document_types'] = DocumentType::all();
        }

        if (Schema::hasTable('departments')) {
            $debug['data_counts']['departments'] = Department::count();
            $debug['sample_data']['departments'] = Department::all();
        }

        if (Schema::hasTable('users')) {
            $debug['data_counts']['users'] = User::count();
            $debug['sample_data']['users'] = User::limit(10)->get(['id', 'name', 'email']);
        }

        return response()->json($debug);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'Database debug failed',
            'message' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
});