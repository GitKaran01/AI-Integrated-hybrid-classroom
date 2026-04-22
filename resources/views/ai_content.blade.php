<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0px; }
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; margin: 0; padding: 0; line-height: 1.5; }
        .header { background-color: #4f46e5; color: #ffffff; padding: 40px; }
        .header h1 { margin: 0; font-size: 28px; text-transform: uppercase; }
        .status-bar { background-color: #fee2e2; color: #b91c1c; padding: 10px 40px; font-weight: bold; font-size: 12px; }
        .content { padding: 40px; }
        .topic-highlight { color: #ef4444; font-size: 24px; font-weight: bold; }
        .strategy-box { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 25px; border-radius: 12px; margin-top: 20px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; padding: 20px; font-size: 10px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="status-bar">⚠️ WEAK TOPIC PERFORMANCE ALERT</div>
    <div class="header">
        <h1>Revision Guide</h1>
        <p>Instructor: {{ $teacher }} | Date: {{ $date }}</p>
    </div>
    <div class="content">
        <p style="font-size: 12px; color: #64748b; font-weight: bold; text-transform: uppercase;">Detected Topic</p>
        <div class="topic-highlight">{{ $topic }} (Rating: {{ $rating }}/5)</div>
        
        <div class="strategy-box">
            <h3 style="color: #4f46e5; margin-top: 0;">🚀 AI Revision Strategy</h3>
            <ul>
                <li><strong>Recap:</strong> Focus on core fundamentals of {{ $topic }} for 20 minutes.</li>
                <li><strong>Practice:</strong> Distribute the specialized AI question bank to the class.</li>
                <li><strong>Discussion:</strong> Hold a 10-minute doubt clearing session.</li>
            </ul>
        </div>
    </div>
    <div class="footer">AI Hybrid Classroom System &copy; 2026</div>
</body>
</html>