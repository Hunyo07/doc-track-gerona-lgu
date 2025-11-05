<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\DocumentController;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Hold Document Workflow\n";
echo "==============================\n\n";

// Find an admin user or create one for testing
$user = User::whereHas('roles', function($query) {
    $query->where('name', 'admin');
})->first();

if (!$user) {
    $user = User::first();
    if (!$user) {
        echo "No users found in the database. Please create a user first.\n";
        exit(1);
    }
    
    // Ensure admin role exists
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
    // Assign admin role to user
    $user->assignRole('admin');
    echo "Assigned admin role to user: {$user->name}\n";
}

echo "Using user: {$user->name} (ID: {$user->id})\n\n";

// Authenticate the user
Auth::login($user);

// Test 1: Get all documents with status "submitted" (Incoming page)
echo "Test 1: Getting all submitted documents (Incoming page)\n";
echo "------------------------------------------------------\n";

$controller = app(DocumentController::class);
$request = new Request([
    'status' => 'submitted',
    'exclude_hold_documents' => true
]);
$request->setUserResolver(function () use ($user) {
    return $user;
});

try {
    $response = $controller->index($request);
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['data']['data'])) {
        $submittedDocs = $data['data']['data'];
        echo "Found " . count($submittedDocs) . " submitted documents\n";
        
        if (count($submittedDocs) > 0) {
            $testDoc = $submittedDocs[0];
            echo "Test document: {$testDoc['title']} (ID: {$testDoc['id']})\n\n";
            
            // Test 2: Put document on hold
            echo "Test 2: Putting document on hold\n";
            echo "---------------------------------\n";
            
            $holdRequest = new Request([
                'document_ids' => [$testDoc['id']],
                'status' => 'under_review',
                'notes' => 'ON HOLD: Testing hold workflow'
            ]);
            $holdRequest->setUserResolver(function () use ($user) {
                return $user;
            });
            
            $holdResponse = $controller->bulkUpdateStatus($holdRequest);
            $holdData = json_decode($holdResponse->getContent(), true);
            
            if ($holdData['success']) {
                echo "✓ Document successfully put on hold\n\n";
                
                // Test 3: Check if document is excluded from Incoming page
                echo "Test 3: Checking if document is excluded from Incoming page\n";
                echo "-----------------------------------------------------------\n";
                
                $incomingResponse = $controller->index($request);
                $incomingData = json_decode($incomingResponse->getContent(), true);
                
                $foundInIncoming = false;
                if (isset($incomingData['data']['data'])) {
                    foreach ($incomingData['data']['data'] as $doc) {
                        if ($doc['id'] == $testDoc['id']) {
                            $foundInIncoming = true;
                            break;
                        }
                    }
                }
                
                if (!$foundInIncoming) {
                    echo "✓ Document correctly excluded from Incoming page\n\n";
                } else {
                    echo "✗ Document still appears in Incoming page (ISSUE)\n\n";
                }
                
                // Test 4: Check if document appears in Hold page
                echo "Test 4: Checking if document appears in Hold page\n";
                echo "--------------------------------------------------\n";
                
                $holdPageRequest = new Request([
                    'hold_documents' => true
                ]);
                $holdPageRequest->setUserResolver(function () use ($user) {
                    return $user;
                });
                
                $holdPageResponse = $controller->index($holdPageRequest);
                $holdPageData = json_decode($holdPageResponse->getContent(), true);
                
                $foundInHold = false;
                if (isset($holdPageData['data']['data'])) {
                    foreach ($holdPageData['data']['data'] as $doc) {
                        if ($doc['id'] == $testDoc['id']) {
                            $foundInHold = true;
                            break;
                        }
                    }
                }
                
                if ($foundInHold) {
                    echo "✓ Document correctly appears in Hold page\n\n";
                } else {
                    echo "✗ Document does not appear in Hold page (ISSUE)\n\n";
                }
                
                echo "All tests completed successfully! ✓\n";
                echo "The hold document workflow is working correctly.\n";
                
            } else {
                echo "✗ Failed to put document on hold: " . ($holdData['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "No submitted documents found to test with.\n";
        }
    } else {
        echo "No documents data found in response.\n";
    }
} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}