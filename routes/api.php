<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\TopicRatingController;
use App\Http\Controllers\Api\TeacherStatusController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TeacherController;
use App\Models\Classroom;
use App\Models\Subject;

use App\Http\Controllers\Api\StudentController;


Route::get('/analytics/class-report', [AnalyticsController::class, 'getClassDailyReport']);

// Specific class ke saare students ko fetch karne ke liye API
Route::get('/students/classroom/{classroomId}', [StudentController::class, 'getByClassroom']);

// Naya student register karne ke liye API
Route::post('/students/register', [StudentController::class, 'registerStudent']);

// 🤖 API FOR MODEL TEAM (To push AI attendance data)
Route::post('/attendance/ai-sync', [AttendanceController::class, 'syncFromAI']);

// 📱 API FOR FLUTTER TEAM (To get attendance for a classroom)
Route::get('/attendance/classroom/{classroomId}', [AttendanceController::class, 'getByClassroom']);


Route::get('/subjects', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\Subject::select('id', 'name')->get()
    ]);
});



Route::get('/classrooms', function () {
    return response()->json([
        'success' => true,
        'data' => Classroom::select('id', 'name')->get()
    ]);
});


Route::get('/attendance', [AttendanceController::class, 'index']);
Route::get('/ai-contents', [AIController::class, 'index']);
Route::post('/generate-ai', [AIController::class, 'generate']);

Route::post('/notifications/revise/{id}', [NotificationController::class, 'reviseAgain']);

// Create notification
Route::post('/notifications', [NotificationController::class, 'store']);

// Get notifications for teacher
Route::get('/notifications/{teacherId}', [NotificationController::class, 'index']);

// Mark as done
Route::post('/notifications/done/{id}', [NotificationController::class, 'markDone']);



// Route::get('/teachers', function () {
//     return \App\Models\Teacher::select('id', 'name')->get();
// });

// Route::post('/teachers', function (Illuminate\Http\Request $request) {

//     $request->validate([
//         'name' => 'required',
//         'password' => 'required|min:4'
//     ]);

//     $teacher = \App\Models\Teacher::create([
//         'name' => $request->name,
//         'password' => \Illuminate\Support\Facades\Hash::make($request->password)
//     ]);

//     return response()->json([
//         'success' => true,
//         'data' => $teacher
//     ]);
// });


// Route::post('/teachers', function (Illuminate\Http\Request $request) {

//     $request->validate([
//         'name' => 'required',
//         'password' => 'required|min:4'
//     ]);

//     $teacher = \App\Models\Teacher::create([
//         'name' => $request->name,
//         'password' => \Illuminate\Support\Facades\Hash::make($request->password)
//     ]);

//     return response()->json([
//         'success' => true,
//         'data' => $teacher
//     ]);
// });


// Route::post('/teachers/update-password/{id}', function ($id, Illuminate\Http\Request $request) {

//     $teacher = \App\Models\Teacher::findOrFail($id);

//     $teacher->update([
//         'password' => \Illuminate\Support\Facades\Hash::make($request->password)
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Password updated'
//     ]);
// });





Route::get('/teachers', [TeacherController::class, 'index']);
Route::post('/teachers', [TeacherController::class, 'store']);
Route::put('/teachers/{id}', [TeacherController::class, 'update']);
Route::delete('/teachers/{id}', [TeacherController::class, 'destroy']);




Route::post('/login', function (Illuminate\Http\Request $request) {
    // 1. Email se teacher ko dhoondo
    $teacher = \App\Models\Teacher::where('email', $request->email)->first();

    // 2. Password check karo (Hash verification ke saath)
    if (!$teacher || !\Illuminate\Support\Facades\Hash::check($request->password, $teacher->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // 3. Response mein saara data bhej rahe hain
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email,
            // Carbon use karke dates ko readable format mein convert kiya
            'member_since' => $teacher->created_at->format('d M Y'), 
            'updated_at' => $teacher->updated_at->diffForHumans() 
        ]
    ]);
});



Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
Route::get('/teacher-stats/{id}', [AnalyticsController::class, 'teacherStats']);
// Sabhi teachers ka aaj ka status dekhne ke liye
Route::get('/teachers-attendance-status', [App\Http\Controllers\Api\TeacherStatusController::class, 'getAllStatuses']);
Route::get('/weak-topics', [AnalyticsController::class, 'weakTopics']);

Route::post('/attendance', [AttendanceController::class, 'store']);
Route::post('/topic-rating', [TopicRatingController::class, 'store']);
Route::post('/teacher-status', [TeacherStatusController::class, 'store']);
Route::post('/generate-ai', [AIController::class, 'generate']);