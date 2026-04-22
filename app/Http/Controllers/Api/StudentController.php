<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Flutter Team yeh API call karegi naya baccha add karne ke liye (POST)
     */
    public function registerStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'classroom_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $student = Student::create([
            'name'         => $request->name,
            'classroom_id' => $request->classroom_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully.',
            'student_id' => $student->id,
            'data' => $student
        ], 201);
    }

    /**
     * Flutter Team yeh API call karegi ek class ke saare baccho ki list dekhne ke liye (GET)
     */
    public function getByClassroom($classroomId)
    {
        $students = Student::where('classroom_id', $classroomId)->get();

        return response()->json([
            'success' => true,
            'total_students' => $students->count(),
            'data' => $students
        ], 200);
    }
}