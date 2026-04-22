<?php

use Illuminate\Support\Facades\Route;
use App\Models\Classroom;

Route::get('/mobile-app', function () {
    return view('flutter-web-app', [
        'classrooms' => \App\Models\Classroom::all()
    ]);
});


Route::get('/dashboard-report', function () {
    // Top row mein classes dikhane ke liye data bhej rahe hain
    $classrooms = Classroom::all(); 
    return view('attendance-report', compact('classrooms'));
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});


// Revsion Center ke routes

use App\Http\Controllers\AiRevisionController;

Route::get('/ai-revision-hub', [AiRevisionController::class, 'index'])->name('ai.revision.hub');
Route::get('/api/weak-topics/{classId}', [AiRevisionController::class, 'getWeakTopics']);
Route::get('/api/revision-history/{classId}', [AiRevisionController::class, 'getHistory']);
Route::post('/api/generate-revision', [AiRevisionController::class, 'generate']);



Route::post('/api/generate-draft', [AiRevisionController::class, 'generateDraft']);
Route::post('/api/finalize-pdf', [AiRevisionController::class, 'finalizePdf']);