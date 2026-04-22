<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 80px 50px; }
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
        .header-box { border-bottom: 2px solid #1a365d; padding-bottom: 15px; margin-bottom: 30px; }
        .main-title { text-align: center; text-transform: uppercase; letter-spacing: 2px; color: #1a365d; margin-bottom: 10px; }
        
        .info-table { width: 100%; border-collapse: collapse; }
        .label { font-weight: bold; color: #555; text-transform: uppercase; font-size: 10px; display: block; margin-bottom: 2px; }
        .value { color: #000; font-weight: bold; font-size: 14px; }

        .content { text-align: justify; font-size: 13px; white-space: pre-wrap; }
        footer { position: fixed; bottom: -40px; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #eee; width: 100%; padding-top: 10px; }
    </style>
</head>
<body>
    <footer>AI Revision Hub Official Document | Generated on {{ date('d-m-Y H:i') }}</footer>

    <div class="main-title">
        <h1 style="margin:0;">Revision Material</h1>
    </div>

    <div class="header-box">
        <table class="info-table">
            <tr>
                <td width="33%">
                    <span class="label">Classroom</span>
                    <span class="value">{{ $classroom_name }}</span>
                </td>
                <td width="33%" style="text-align: center;">
                    <span class="label">Subject/Topic</span>
                    <span class="value">{{ $topic }}</span>
                </td>
                <td width="33%" style="text-align: right;">
                    <span class="label">Date</span>
                    <span class="value">{{ $date }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        {!! nl2br(e($content)) !!}
    </div>
</body>
</html>




<!-- <!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .topic-name { color: #3b82f6; font-size: 24px; text-transform: uppercase; }
        .section-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-top: 10px; }
        pre { white-space: pre-wrap; font-family: 'Helvetica', sans-serif; font-size: 14px; }
        .footer { margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="topic-name">AI Revision Sheet: {{ $topic }}</div>
        <p>Generated for Classroom Content • {{ date('d M Y') }}</p>
    </div>

    <div class="section-box">
        <pre>{{ $content }}</pre>
    </div>

    <div class="footer">
        © AI Hybrid Classroom | Smart Revision Module
    </div>
</body>
</html> -->