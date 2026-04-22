<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\TopicRating;
use App\Models\AIContent;
use App\Models\Classroom;


class AnalyticsController extends Controller
{
    // 🔹 DASHBOARD DATA
    public function dashboard(Request $request)
    {
        $teacherId = $request->teacher_id;

        // ✅ Total Attendance (NO teacher_id ❌)
        $attendance = Attendance::count();

        // ✅ Weak Topics (teacher-based ✅)
        $weakTopics = TopicRating::when($teacherId, function ($query) use ($teacherId) {
                return $query->where('teacher_id', $teacherId);
            })
            ->where('rating', '<', 3)
            ->count();

        // ✅ AI Generated Content
        $aiGenerated = AIContent::count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_attendance' => $attendance,
                'weak_topics' => $weakTopics,
                'ai_generated' => $aiGenerated
            ]
        ]);
    }

    // 🔹 TEACHER GRAPH DATA
    public function teacherStats($teacherId)
    {
        $data = TopicRating::where('teacher_id', $teacherId)
            ->where('rating', '<', 3)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as weak_count')
            ->groupBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function weakTopics()
{
    $data = \App\Models\TopicRating::select('topic_id')
        ->groupBy('topic_id')
        ->havingRaw('AVG(rating) < 3.5')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}


public function getClassDailyReport(Request $request)
{
    $classId = $request->query('class_id');
    $date = $request->query('date', now()->toDateString());

    // 1. Get Classroom and Total Students count
    $classroom = Classroom::withCount('students')->findOrFail($classId);
    $totalStudents = $classroom->students_count;

    // 2. Get Present Students for this date
    $presentStudents = Attendance::where('classroom_id', $classId)
        ->where('date', $date)
        ->with('student:id,name')
        ->get();

    $presentCount = $presentStudents->count();
    $absentCount = $totalStudents - $presentCount;

    // 3. Format Student List
    $studentList = $presentStudents->map(function($att) {
        return [
            'id' => $att->student_id,
            'name' => $att->student->name ?? 'Unknown'
        ];
    });

    return response()->json([
        'success' => true,
        'summary' => [
            'total' => $totalStudents,
            'present' => $presentCount,
            'absent' => $absentCount
        ],
        'students' => $studentList
    ]);
}
}