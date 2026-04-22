<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Topic;

class NotificationController extends Controller
{
    // 🔹 1. CREATE NOTIFICATION
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'title' => 'required',
            'message' => 'required',
            'type' => 'required'
        ]);

        $notification = Notification::create([
            'teacher_id' => $request->teacher_id,
            'classroom_id' => $request->classroom_id,
            'topic_id' => $request->topic_id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'due_date' => $request->due_date
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification created',
            'data' => $notification
        ]);
    }

    // 🔹 2. FETCH NOTIFICATIONS (BY TEACHER)
    // 🔹 FETCH NOTIFICATIONS (BY TEACHER)
    public function index($teacherId)
    {
        // Sabse pehle notifications fetch karo
        $notifications = Notification::where('teacher_id', $teacherId)
            ->latest()
            ->get();

        $data = $notifications->map(function ($n) {

            // Topic name nikalne ke liye
            $topic = Topic::find($n->topic_id);

            return [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'topic_name' => $topic ? $topic->name : 'Unknown Topic',

                // 🔥 FIX: AIContent table mein dekhne ki bajaye 
                // seedha Notification model ka pdf_url use karo jo humne save kiya tha
                'pdf_url'    => $n->pdf_url ? $n->pdf_url : null
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    // Baki store, markDone aur reviseAgain methods same rahenge...


    // 🔹 3. MARK AS DONE
    public function markDone($id)
    {
        $notification = Notification::findOrFail($id);

        // Status update karo
        $notification->update(['is_done' => 1]);

        // Ab delete kar do taki Flutter ki list mein na aaye
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification completed and removed from list'
        ]);
    }

    // 🔹 4. REVISE AGAIN
    public function reviseAgain($id)
    {
        $notification = Notification::findOrFail($id);

        $notification->update([
            'action' => 'revise',
            'is_done' => false,
            'due_date' => now()->addDays(2) // ⏰ remind after 2 days
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marked for revision again'
        ]);
    }
}
