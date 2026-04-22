<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * 🤖 API FOR MODEL TEAM
     * Receive recognized faces from Python AI and save to database
     */
    public function syncFromAI(Request $request)
    {
        // 1. Validate the incoming JSON from the AI Model
        $validator = Validator::make($request->all(), [
            'classroom_id'          => 'required|integer',
            'date'                  => 'required|date',
            'students'              => 'required|array',
            'students.*.student_id' => 'required|integer',
            'students.*.confidence' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $classroomId = $request->classroom_id;
        $date = $request->date;
        $savedCount = 0;

        // 2. Loop through each student detected by AI
        foreach ($request->students as $aiStudent) {
            
            // 🛑 SECURITY CHECK: Ensure this student ID actually exists in your database!
            $studentExists = DB::table('students')->where('id', $aiStudent['student_id'])->exists();

            if ($studentExists) {
                // 3. Save or Update 
                Attendance::updateOrCreate(
                    [
                        'student_id'   => $aiStudent['student_id'],
                        'classroom_id' => $classroomId,
                        'date'         => $date
                    ],
                    [
                        'status'       => 'present',
                        'confidence'   => $aiStudent['confidence']
                    ]
                );
                $savedCount++;
            }
        }

        // 4. Return success response to the Model Team
        return response()->json([
            'success' => true,
            'message' => "Attendance synced successfully. $savedCount valid students marked present."
        ], 200);
    }

    /**
     * 📱 API FOR FLUTTER TEAM
     * Fetch attendance list for a specific classroom (WITH STUDENT NAMES)
     */
    public function getByClassroom($classroomId, Request $request)
    {
        // Flutter can request a specific date ?date=2026-04-10, otherwise it defaults to today
        $date = $request->query('date', now()->toDateString());

        // ✅ THE MAGIC FIX: with('student:id,name') will automatically bring the student's name from DB
        $attendances = Attendance::with('student:id,name')
                                 ->where('classroom_id', $classroomId)
                                 ->where('date', $date)
                                 ->get();

        return response()->json([
            'success' => true,
            'classroom_id' => $classroomId,
            'date' => $date,
            'total_present' => $attendances->count(),
            'data' => $attendances
        ], 200);
    }
}