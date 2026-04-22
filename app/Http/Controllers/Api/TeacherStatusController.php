<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherStatus;
use App\Models\TopicRating;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TeacherStatusController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'status' => 'required|in:present,absent'
        ]);

        try {
            $today = date('Y-m-d');

            // 2. Save/Update teacher status
            TeacherStatus::updateOrCreate(
                [
                    'teacher_id' => $request->teacher_id,
                    'date' => $today
                ],
                [
                    'status' => $request->status
                ]
            );

            // 🔥 IF TEACHER IS ABSENT
          if ($request->status === 'absent') {
    $teacher = \App\Models\Teacher::find($request->teacher_id); // Naam nikaalne ke liye
    
    Notification::updateOrCreate(
        ['teacher_id' => $request->teacher_id, 'type' => 'absent', 'created_at' => date('Y-m-d')],
        [
            'title'   => 'Absent Alert: ' . $teacher->name, // Naam add ho gaya
            'message' => $teacher->name . ' is marked absent for today.',
        ]
    );
}

            return response()->json(['success' => true, 'message' => 'Status saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getAllStatuses()
    {
        try {
            // Teachers table ko statuses table se join kar rahe hain
            $statuses = DB::table('teachers')
                ->leftJoin('teacher_statuses', function ($join) {
                    $join->on('teachers.id', '=', 'teacher_statuses.teacher_id')
                        ->whereDate('teacher_statuses.date', '=', date('Y-m-d'));
                })
                ->select(
                    'teachers.id',
                    'teachers.name',
                    DB::raw('IFNULL(teacher_statuses.status, "not marked") as current_status'),
                    'teacher_statuses.date'
                )
                ->get();

            return response()->json([
                'success' => true,
                'date' => date('Y-m-d'),
                'data' => $statuses
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
