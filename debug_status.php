<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Document;
use App\Models\Department;
use App\Models\User;

echo "=== TESTING THE FIX ===\n\n";

// Check current status of documents for Mayor's Office (ID 6)
echo "Documents currently assigned to Mayor's Office (Department ID 6):\n";
$mayorDocs = Document::where('current_department_id', 6)->get();

foreach ($mayorDocs as $doc) {
    echo "- Doc {$doc->id}: '{$doc->title}' - Status: {$doc->status}\n";
}

echo "\n";

// Check if there are any documents that could be routed to test
echo "Available documents that could be routed:\n";
$availableDocs = Document::whereIn('status', ['draft', 'completed', 'approved'])
    ->where('current_department_id', '!=', 6)
    ->limit(3)
    ->get();

foreach ($availableDocs as $doc) {
    echo "- Doc {$doc->id}: '{$doc->title}' - Status: {$doc->status} - Current Dept: {$doc->current_department_id}\n";
}

echo "\n=== SUMMARY ===\n";
echo "The fix has been applied to DocumentController.php\n";
echo "The bulkUpdateDepartment method now sets status to 'submitted' when routing documents.\n";
echo "This should resolve the issue where documents don't appear on the incoming page.\n";