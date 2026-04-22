<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AIContent;
use Barryvdh\DomPDF\Facade\Pdf;

class AIController extends Controller
{
    public function generate(Request $request)
    {
        // ✅ VALIDATION
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'topic_id' => 'required|exists:topics,id',
            'topic_name' => 'required|string'
        ]);

        try {

            $teacherId = $request->teacher_id;
            $classroomId = $request->classroom_id;
            $topicId = $request->topic_id;
            $topicName = $request->topic_name;

            // 🔥 FAKE AI CONTENT
            $questions = [
                "What is $topicName?",
                "Explain $topicName in detail",
                "Give examples of $topicName"
            ];

            $answers = [
                "$topicName is an important concept...",
                "$topicName works like...",
                "Examples include..."
            ];

            // ✅ Ensure folder exists
            if (!file_exists(public_path('pdfs'))) {
                mkdir(public_path('pdfs'), 0777, true);
            }

            // 🔥 GENERATE PDF
            $pdf = Pdf::loadView('pdf.ai_content', [
                'topic' => $topicName,
                'questions' => $questions,
                'answers' => $answers
            ]);

            $fileName = 'ai_' . time() . '.pdf';
            $pdfPath = public_path('pdfs/' . $fileName);

            $pdf->save($pdfPath);

            // ✅ IMPORTANT: FULL URL (FIXED)
            $pdfUrl = url('/pdfs/' . $fileName);

            // 🔥 SAVE TO DB
            $content = AIContent::create([
                'teacher_id' => $teacherId,
                'classroom_id' => $classroomId, // ✅ MUST BE HERE
                'topic_id' => $topicId,
                'questions' => json_encode($questions),
                'answers' => json_encode($answers),
                 'pdf_url' => url('/pdfs/' . $fileName) // ✅ FIXED
            ]);

            return response()->json([
                'success' => true,
                'data' => $content
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function index(Request $request)
{
    $teacherId = $request->teacher_id;

    $data = \App\Models\AIContent::where('teacher_id', $teacherId)
        ->select('id', 'topic_id', 'pdf_url', 'created_at')
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}
}
