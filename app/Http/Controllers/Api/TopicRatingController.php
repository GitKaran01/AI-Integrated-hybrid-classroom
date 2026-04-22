<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TopicRating;
use App\Models\Notification;
use App\Models\Topic;
use App\Models\Teacher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class TopicRatingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'teacher_id'   => 'required|exists:teachers,id',
                'classroom_id' => 'required|exists:classrooms,id',
                'subject_id'   => 'required|exists:subjects,id',
                'topic_name'   => 'required|string',
                'rating'       => 'required|numeric|min:1|max:5',
                'label'        => 'required|string'
            ]);

            DB::beginTransaction();

            $topic = Topic::firstOrCreate([
                'name'         => $request->topic_name,
                'subject_id'   => $request->subject_id,
                'classroom_id' => $request->classroom_id
            ]);

            TopicRating::create([
                'teacher_id'   => $request->teacher_id,
                'topic_id'     => $topic->id,
                'rating'       => $request->rating,
                'label'        => $request->label,
                'classroom_id' => $request->classroom_id
            ]);

            $avg = TopicRating::where('topic_id', $topic->id)->avg('rating');
            $pdfFileName = null;

           if ($avg < 3.5) {
    // 1. PDF Naming aur Path
    $cleanName = Str::slug($topic->name, '_');
    $timestamp = now()->format('Y-m-d_H-i-s');
    $pdfFileName = "{$cleanName}_{$timestamp}.pdf";
    $pdfPath = public_path('pdfs/' . $pdfFileName);

    // 2. Folder Check
    if (!File::isDirectory(public_path('pdfs'))) {
        File::makeDirectory(public_path('pdfs'), 0777, true, true);
    }

    // 3. PDF Generate
    $teacher = Teacher::find($request->teacher_id);
    $pdfData = [
        'topic'    => $topic->name,
        'date'     => now()->format('d M Y'),
        'time'     => now()->format('h:i A'),
        'teacher'  => $teacher->name ?? 'Faculty',
        'rating'   => round($avg, 1)
    ];
    $pdf = Pdf::loadView('ai_content', $pdfData);
    $pdf->save($pdfPath);

    // --- YE HAI MAIN FIX ---
    // Hum wahi link generate kar rahe hain jo Postman mein dikh raha tha
    $finalPdfUrl = url('pdfs/' . $pdfFileName); 

    // 4. Purani entry delete karke Nayi Notification Save karo
    Notification::where('teacher_id', $request->teacher_id)
                ->where('topic_id', $topic->id)
                ->where('type', 'weak_topic')
                ->delete();

    Notification::create([
        'teacher_id'   => $request->teacher_id,
        'classroom_id' => $request->classroom_id,
        'topic_id'     => $topic->id,
        'title'        => 'Weak Topic: ' . $topic->name,
        'message'      => 'AI has generated a revision guide for this weak topic.',
        'pdf_url'      => $finalPdfUrl, // <--- AB YE DB ME STORE HOGA
        'type'         => 'weak_topic',
        'is_done'      => 0
    ]);
}
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rating saved. PDF: ' . ($pdfFileName ?? 'None'),
                'debug_url' => $generatedUrl ?? null // Check this in Postman
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}