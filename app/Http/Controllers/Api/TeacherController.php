<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher;


class TeacherController extends Controller
{
    // ✅ GET ALL TEACHERS
    public function index()
    {
      
    $teachers = Teacher::all();

        return response()->json([
            'success' => true,
            'data' => $teachers
        ], 200);
    }

    // ✅ CREATE TEACHER
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:teachers,email',
            'password' => 'required|min:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $teacher = Teacher::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password) // Always hashing for security
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher created successfully',
            'data'    => $teacher
        ], 201);
    }

    // ✅ UPDATE TEACHER
    public function update(Request $request, $id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $teacher->name = $request->name;
        $teacher->email = $request->email;

        // Only update password if provided
        if ($request->has('password') && !empty($request->password)) {
            $teacher->password = Hash::make($request->password);
        }

        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully',
            'data'    => $teacher
        ], 200);
    }

    // ✅ DELETE TEACHER
    public function destroy($id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully'
        ], 200);
    }
}