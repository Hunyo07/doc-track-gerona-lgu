<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Departments:\n";
$departments = App\Models\Department::all(['id', 'name', 'code']);
foreach ($departments as $dept) {
    echo $dept->id . ': ' . $dept->name . ' (' . $dept->code . ')' . "\n";
}

echo "\nDocuments with current_department_id:\n";
$docs = App\Models\Document::whereNotNull('current_department_id')->get(['id', 'title', 'current_department_id', 'status']);
foreach ($docs as $doc) {
    echo 'Doc ' . $doc->id . ': ' . $doc->title . ' -> Dept ' . $doc->current_department_id . ' (Status: ' . $doc->status . ')' . "\n";
}

echo "\nUsers in Mayor Department:\n";
$mayorDept = App\Models\Department::where('code', 'MAYOR')->first();
if ($mayorDept) {
    echo 'Mayor Dept ID: ' . $mayorDept->id . "\n";
    $users = App\Models\User::where('department_id', $mayorDept->id)->get(['id', 'name', 'email']);
    foreach ($users as $user) {
        echo 'User ' . $user->id . ': ' . $user->name . ' (' . $user->email . ')' . "\n";
    }
} else {
    echo "Mayor department not found!\n";
}

echo "\nAll documents:\n";
$allDocs = App\Models\Document::all(['id', 'title', 'department_id', 'current_department_id', 'status']);
foreach ($allDocs as $doc) {
    echo 'Doc ' . $doc->id . ': ' . $doc->title . ' | Orig Dept: ' . $doc->department_id . ' | Current Dept: ' . $doc->current_department_id . ' | Status: ' . $doc->status . "\n";
}