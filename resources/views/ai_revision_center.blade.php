<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Revision Hub | Next-Gen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { 
            --bg: #05080f; 
            --glass: rgba(15, 23, 42, 0.6); 
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent: #3b82f6; 
            --accent-glow: rgba(59, 130, 246, 0.5);
            --text: #f8fafc; 
            --success: #10b981;
        }

        body { 
            background: radial-gradient(circle at 0% 0%, #1e293b 0%, #05080f 100%); 
            color: var(--text); 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent); }

        .container { max-width: 1200px; margin: auto; }

        h1 { 
            font-weight: 800; 
            letter-spacing: -1px; 
            display: flex; 
            align-items: center; 
            gap: 15px;
            text-shadow: 0 0 20px var(--accent-glow);
        }

        /* --- Classroom Horizontal Switcher --- */
        .classroom-container {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 10px 5px 20px 5px;
            margin-bottom: 30px;
            scroll-behavior: smooth;
        }

        .class-card {
            min-width: 180px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 20px;
            border-radius: 18px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
            backdrop-filter: blur(12px);
        }

        .class-card:hover { transform: translateY(-5px); border-color: var(--accent); }
        .class-card.active { 
            background: var(--accent); 
            box-shadow: 0 10px 30px var(--accent-glow);
            border-color: transparent;
        }

        .class-card i { font-size: 24px; margin-bottom: 10px; display: block; }

        /* --- Main Workspace Layout --- */
        .workspace {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 25px;
        }

        @media (max-width: 900px) { .workspace { grid-template-columns: 1fr; } }

        .glass-panel {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        /* --- Scrollable Weak Topics Box --- */
        .topics-wrapper {
            max-height: 500px;
            overflow-y: auto;
            margin-top: 15px;
            padding-right: 10px;
        }

        .topic-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(255,255,255,0.03);
            border-radius: 15px;
            margin-bottom: 10px;
            border: 1px solid transparent;
            transition: 0.3s;
        }

        .topic-item:hover {
            border-color: var(--glass-border);
            background: rgba(255,255,255,0.07);
        }

        /* --- Editor Styles --- */
        textarea {
            width: 100%;
            height: 400px;
            background: rgba(0,0,0,0.3);
            color: #cbd5e1;
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        textarea:focus { border-color: var(--accent); box-shadow: 0 0 15px var(--accent-glow); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }

        .btn-blue { background: var(--accent); color: white; box-shadow: 0 4px 15px var(--accent-glow); }
        .btn-green { background: var(--success); color: white; }
        .btn:hover { transform: scale(1.02); filter: brightness(1.1); }
        .btn:disabled { opacity: 0.5; transform: none; }

        input[type="text"] {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            padding: 12px 15px;
            border-radius: 12px;
            color: white;
            outline: none;
            width: 100%;
        }

        /* --- History Grid --- */
        .history-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .history-card {
            background: rgba(255,255,255,0.03);
            padding: 15px;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #loader { color: var(--accent); font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h1><i class="fa-solid fa-wand-magic-sparkles"></i> AI Revision Hub</h1>

    <p style="color: #64748b; margin-bottom: 10px; font-weight: 600;">SELECT CLASSROOM</p>
    <div class="classroom-container" id="classSwitcher">
        @foreach($classrooms as $c)
            <div class="class-card" onclick="selectClass(this, '{{ $c->id }}')">
                <i class="fa-solid fa-chalkboard-user"></i>
                <div style="font-weight: bold;">{{ $c->name }}</div>
                <div style="font-size: 11px; opacity: 0.6; margin-top: 5px;">ID: #{{ $c->id }}</div>
            </div>
        @endforeach
    </div>

    <div class="workspace">
        <div class="glass-panel" id="topicsList" style="display:none;">
            <h3 style="margin-top:0;"><i class="fa-solid fa-bolt" style="color:#fbbf24"></i> Weak Topics</h3>
            <div class="topics-wrapper" id="topicsBody">
                </div>
        </div>

        <div class="glass-panel" id="editorSection" style="display:none;">
            <h3 id="editingTitle" style="margin-top:0;">AI Editor</h3>
            
            <div id="loader" style="display:none;">
                <i class="fa-solid fa-atom fa-spin"></i> Gemini is thinking...
            </div>

            <textarea id="aiDraftArea" placeholder="Drafting your excellence..."></textarea>
            
            <div style="display:flex; gap:10px; margin: 15px 0;">
                <input type="text" id="refineInput" placeholder="How should I refine this?">
                <button id="refineBtn" onclick="refine()" class="btn btn-green"><i class="fa-solid fa-wand-sparkles"></i> Update</button>
            </div>
            
            <button id="finalBtn" onclick="finalize()" class="btn btn-blue" style="width:100%;">
                <i class="fa-solid fa-file-pdf"></i> Finalize & Generate PDF
            </button>
        </div>
    </div>

    <div class="glass-panel" id="historySection" style="display:none; margin-top: 25px;">
        <h3 style="margin-top:0;"><i class="fa-solid fa-clock-rotate-left"></i> Revision History</h3>
        <div class="history-grid" id="historyBody"></div>
    </div>
</div>

<script>
    let activeTopic = "";
    let isTyping = false;
    let selectedClassId = "";

    // Switcher Logic
    function selectClass(el, classId) {
        // UI Update
        document.querySelectorAll('.class-card').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        
        // Data Load
        selectedClassId = classId;
        fetchTopics(classId);
    }

    // --- Core Logic (Unchanged but UI Injected) ---
    function typeWriter(text, elementId, speed = 10) {
        let i = 0;
        let element = document.getElementById(elementId);
        element.value = ""; 
        isTyping = true;
        document.getElementById('finalBtn').disabled = true;
        document.getElementById('refineBtn').disabled = true;

        function type() {
            if (i < text.length) {
                element.value += text.charAt(i);
                i++;
                element.scrollTop = element.scrollHeight;
                setTimeout(type, speed);
            } else {
                isTyping = false;
                document.getElementById('finalBtn').disabled = false;
                document.getElementById('refineBtn').disabled = false;
                document.getElementById('loader').style.display = 'none';
            }
        }
        type();
    }

    async function fetchTopics(classId) {
        document.getElementById('topicsList').style.display = 'block';
        document.getElementById('historySection').style.display = 'block';
        document.getElementById('topicsBody').innerHTML = `<p><i class="fa-solid fa-spinner fa-spin"></i> Scanning performance...</p>`;
        
        const res = await fetch(`/api/weak-topics/${classId}`);
        const data = await res.json();
        
        document.getElementById('topicsBody').innerHTML = data.map(t => `
            <div class="topic-item">
                <span>${t.topic.name}</span>
                <button onclick="startDraft('${t.topic.name}')" class="btn btn-blue" style="padding:6px 12px; font-size:12px;">Edit</button>
            </div>
        `).join('') || '<p>All topics look good!</p>';

        loadHistory(classId);
    }

    async function startDraft(topic) {
        if(isTyping) return;
        activeTopic = topic;
        document.getElementById('editorSection').style.display = 'block';
        document.getElementById('editingTitle').innerText = "Revision: " + topic;
        document.getElementById('aiDraftArea').value = "";
        document.getElementById('loader').style.display = 'flex';
        
        // Scroll to editor on mobile
        if(window.innerWidth < 900) document.getElementById('editorSection').scrollIntoView({behavior: 'smooth'});

        try {
            const res = await fetch('/api/generate-draft', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ topic: topic })
            });
            const data = await res.json();
            if(data.success) typeWriter(data.draft, 'aiDraftArea');
            else { alert(data.error); document.getElementById('loader').style.display = 'none'; }
        } catch (e) { document.getElementById('loader').style.display = 'none'; }
    }

    async function refine() {
        if(isTyping) return;
        const instr = document.getElementById('refineInput').value;
        const current = document.getElementById('aiDraftArea').value;
        if(!instr) return;

        document.getElementById('loader').style.display = 'flex';
        document.getElementById('aiDraftArea').value = "";

        const res = await fetch('/api/generate-draft', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ topic: activeTopic, instruction: "Current: " + current + "\nChange: " + instr })
        });
        const data = await res.json();
        if(data.success) {
            typeWriter(data.draft, 'aiDraftArea');
            document.getElementById('refineInput').value = "";
        }
    }

    async function finalize() {
        if(isTyping) return;
        const content = document.getElementById('aiDraftArea').value;
        const res = await fetch('/api/finalize-pdf', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ content: content, topic: activeTopic, classroom_id: selectedClassId })
        });
        const data = await res.json();
        if(data.success) {
            window.open(data.url, '_blank');
            loadHistory(selectedClassId);
        }
    }

    async function loadHistory(classId) {
        const res = await fetch(`/api/revision-history/${classId}`);
        const data = await res.json();
        document.getElementById('historyBody').innerHTML = data.map(h => `
            <div class="history-card">
                <div>
                    <strong>${h.topic_name}</strong><br>
                    <small style="opacity:0.5">${new Date(h.created_at).toLocaleDateString()}</small>
                </div>
                <a href="${h.pdf_path}" target="_blank" class="btn btn-blue" style="padding:5px 10px; font-size:11px; text-decoration:none;">View PDF</a>
            </div>
        `).join('') || '<p>No history.</p>';
    }
</script>
</body>
</html>