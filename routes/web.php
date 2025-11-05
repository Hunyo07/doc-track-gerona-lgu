<?php

use Illuminate\Support\Facades\Route;

// Public document view route for QR code scanning
Route::get('/documents/code/{document_number}', function ($document_number) {
    return view('app');
})->name('document.code.view');

// Serve the Vue SPA for all routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
Route::get('/phpinfo', function () {
    return phpinfo();
});