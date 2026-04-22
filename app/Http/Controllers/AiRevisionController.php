<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TopicRating;
use App\Models\AiRevisionMaterial;
use App\Models\Classroom;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class AiRevisionController extends Controller
{
    // ✅ Screenshot wali correct key yahan hai
    private $geminiApiKey = 'AIzaSyCEGmI7G4HTezfcKEMgbSGL48seT3s7CH0';

    public function index()
    {
        $classrooms = Classroom::all();
        return view('ai_revision_center', compact('classrooms'));
    }

    public function getWeakTopics($classId)
    {
        $topics = TopicRating::where('classroom_id', $classId)
            ->select('topic_id', DB::raw('AVG(rating) as avg_rating'))
            ->groupBy('topic_id')
            ->having('avg_rating', '<', 3.5)
            ->with('topic')
            ->get();
        return response()->json($topics);
    }

    public function getHistory($classId)
    {
        $history = AiRevisionMaterial::where('classroom_id', $classId)->latest()->get();
        return response()->json($history);
    }

public function generateDraft(Request $request) {
    $topic = $request->topic;
    $instruction = $request->instruction ?? "Create a detailed revision guide.";
    $apiKey = $this->geminiApiKey;

    // 🔴 TARGETING THE MODEL FROM YOUR SCREENSHOT: gemini-2.5-flash
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

    $payload = [
        "contents" => [
            ["parts" => [["text" => "Act as an expert teacher. Topic: $topic. Instruction: $instruction. Provide a clear summary and practice questions."]]]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 2048
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return response()->json([
            'success' => true,
            'draft' => $result['candidates'][0]['content']['parts'][0]['text']
        ]);
    } else {
        $errorMsg = $result['error']['message'] ?? 'Generation failed with Gemini 2.5';
        return response()->json([
            'success' => false,
            'error' => "Google Error ($httpCode): " . $errorMsg
        ], 500);
    }
}

public function finalizePdf(Request $request)
{
    try {
        $content = $request->content;
        $topic = $request->topic;
        $classId = $request->classroom_id;

        $classroom = Classroom::find($classId);
        
        // Cleaning Markdown stars/hashes
        $cleanContent = preg_replace('/[\*\#\-]{2,}/', '', $content);

        $pdfData = [
            'content'        => $cleanContent,
            'topic'          => $topic,
            'classroom_name' => $classroom->name,
            'date'           => date('l, d F Y'), // Formal Long Date
        ];

        $fileName = "Revision_" . Str::slug($topic) . "_" . time() . ".pdf";
        $path = public_path('revision_pdfs/' . $fileName);

        if (!File::isDirectory(public_path('revision_pdfs'))) {
            File::makeDirectory(public_path('revision_pdfs'), 0777, true, true);
        }

        $pdf = Pdf::loadView('pdf.revision_template', $pdfData);
        $pdf->save($path);

        AiRevisionMaterial::create([
            'classroom_id' => $classId,
            'topic_name'   => $topic,
            'pdf_path'     => url('revision_pdfs/' . $fileName)
        ]);

        return response()->json(['success' => true, 'url' => url('revision_pdfs/' . $fileName)]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
//    public function finalizePdf(Request $request)
// {
//     try {
//         $content = $request->content;
//         $topic = $request->topic;
//         $classId = $request->classroom_id;

//         // 🧹 Markdown Clean-up Logic
//         // Ye line saare **, ###, aur --- ko dhoond kar hata degi
//         $cleanContent = preg_replace('/[\*\#\-]{2,}/', '', $content);
        
//         // Extra spacing theek karne ke liye (Optional)
//         $cleanContent = trim($cleanContent);

//         // PDF File Name setup
//         $fileName = "final_revision_" . Str::slug($topic) . "_" . time() . ".pdf";
//         $path = public_path('revision_pdfs/' . $fileName);

//         // Folder create karna agar nahi hai toh
//         if (!File::isDirectory(public_path('revision_pdfs'))) {
//             File::makeDirectory(public_path('revision_pdfs'), 0777, true, true);
//         }

//         // 📄 PDF generate karna (Clean content ke saath)
//         $pdf = Pdf::loadView('pdf.revision_template', [
//             'content' => $cleanContent, 
//             'topic' => $topic
//         ]);
        
//         $pdf->save($path);

//         // Database mein entry
//         AiRevisionMaterial::create([
//             'classroom_id' => $classId,
//             'topic_name' => $topic,
//             'pdf_path' => url('revision_pdfs/' . $fileName)
//         ]);

//         return response()->json([
//             'success' => true, 
//             'url' => url('revision_pdfs/' . $fileName)
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false, 
//             'error' => "PDF Error: " . $e->getMessage()
//         ], 500);
//     }
// }
}