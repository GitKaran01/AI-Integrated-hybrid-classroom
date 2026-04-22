<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Hybrid Classroom | Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg: #0b0f1a;
            --glass: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.3);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --success: #10b981;
            --danger: #f43f5e;
        }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #1e293b, #0b0f1a);
            color: var(--text-main);
            height: 100vh;
            overflow: hidden;
        }

        .app-container {
            display: grid;
            grid-template-columns: 1fr 380px;
            grid-template-rows: 80px 1fr;
            height: 100vh;
        }

        header {
            grid-column: 1 / 3;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            z-index: 50;
        }

        .main-content {
            padding: 30px 40px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 10px; }

        .side-panel {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border-left: 1px solid var(--glass-border);
            padding: 30px;
            overflow-y: auto;
        }

        .glass-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 20px;
        }

        .glass-card:hover {
            border-color: var(--accent);
            box-shadow: 0 0 20px var(--accent-glow);
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-val { font-size: 36px; font-weight: 800; margin-top: 8px; color: var(--accent); text-shadow: 0 0 15px var(--accent-glow); }

        select, input {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
            margin-bottom: 8px;
        }

        .teacher-item {
            background: rgba(255,255,255,0.03);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid var(--glass-border);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-report { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; }
        .btn-ai-hub { background: linear-gradient(135deg, var(--accent), #1d4ed8); color: white; }
        .btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .dashboard-toolbar {
            display: flex;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 40px;
            width: 100%;
        }

        .teacher-context-box { flex: 1; max-width: 350px; }
        .action-buttons-box { display: flex; gap: 15px; flex: 2; }

        .section-title { font-size: 14px; font-weight: 800; color: var(--accent); letter-spacing: 1px; margin-bottom: 15px; text-transform: uppercase; display: flex; align-items: center; gap: 10px; }
        .status-box-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .status-item { background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 18px; padding: 15px; cursor: pointer; transition: 0.3s; }
        .status-item:hover { background: rgba(255,255,255,0.08); border-color: var(--accent); }

        .loader-overlay {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--accent);
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            z-index: 1000;
            display: none;
        }

        #popup {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(12px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: #1e293b;
            padding: 40px;
            border-radius: 32px;
            width: 400px;
            border: 1px solid var(--glass-border);
        }
    </style>
</head>
<body>

<div id="loader" class="loader-overlay">🛰️ REFRESHING DATA...</div>

<div class="app-container">
    <header>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="background: var(--accent); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-brain"></i>
            </div>
            <h2 style="margin:0; letter-spacing: -0.5px;">AI Hybrid <span style="color: var(--accent);">Classroom</span></h2>
        </div>
    </header>

    <div class="main-content">
        <div class="dashboard-toolbar">
            <div class="teacher-context-box">
                <p style="color: var(--text-dim); margin-bottom: 8px; font-size: 14px; font-weight: 600; text-transform: uppercase;">Active Teacher Context</p>
                <select id="teacherDropdown" onchange="switchTeacher(this.value)"></select>
            </div>

            <div class="action-buttons-box">
                <a href="{{ route('ai.revision.hub') }}" class="btn btn-ai-hub" style="height: 48px; flex: 1;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> AI Revision Hub
                </a>
                <a href="{{ url('/dashboard-report') }}" class="btn btn-report" style="height: 48px; flex: 1.5;">
                    <i class="fa-solid fa-chart-pie"></i> Student Attendance Analytics
                </a>
            </div>
        </div>

        <div class="glass-card">
            <div class="section-title"><i class="fa-solid fa-chalkboard-user"></i> Teacher Status Hub</div>
            <div class="status-box-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="status-item" onclick="fetchTeacherDetailedList('present')" style="border-left: 4px solid var(--success);">
                    <p style="color: var(--text-dim); font-size: 11px; margin:0;">TOTAL PRESENT</p>
                    <div class="stat-val" id="total-present" style="font-size: 28px; margin:0; color: var(--success);">0</div>
                </div>
                <div class="status-item" onclick="fetchTeacherDetailedList('absent')" style="border-left: 4px solid var(--danger);">
                    <p style="color: var(--text-dim); font-size: 11px; margin:0;">TOTAL ABSENT</p>
                    <div class="stat-val" id="total-absent" style="font-size: 28px; margin:0; color: var(--danger);">0</div>
                </div>
            </div>
            <div id="teacherDetailArea" style="display:none; margin-top:15px; border-top: 1px solid var(--glass-border); padding-top:15px;"></div>
        </div>

        <div class="stats-grid">
            <div class="glass-card">
                <i class="fa-solid fa-calendar-check" style="color: var(--accent)"></i>
                <p style="color: var(--text-dim); font-size: 13px; font-weight: 700; margin-top: 15px; text-transform: uppercase;">Teacher Stats</p>
                <div class="stat-val" id="stat-attendance">0</div>
            </div>
            <div class="glass-card">
                <i class="fa-solid fa-triangle-exclamation" style="color: #fbbf24"></i>
                <p style="color: var(--text-dim); font-size: 13px; font-weight: 700; margin-top: 15px; text-transform: uppercase;">Weak Topics</p>
                <div class="stat-val" id="stat-weak" style="color: #fbbf24;">0</div>
            </div>
            <div class="glass-card">
                <i class="fa-solid fa-robot" style="color: var(--success)"></i>
                <p style="color: var(--text-dim); font-size: 13px; font-weight: 700; margin-top: 15px; text-transform: uppercase;">AI Tasks</p>
                <div class="stat-val" id="stat-ai" style="color: var(--success);">0</div>
            </div>
        </div>

        <div style="margin-top: 40px;">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-bell" style="color: var(--accent)"></i> Notifications (Today)
            </h3>
            <div id="notificationList"></div>
        </div>
    </div>

    <div class="side-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h3 style="margin:0">Faculty Management</h3>
            <button class="btn btn-add-teacher" onclick="togglePopup(true)" style="padding:0; width:40px; height:40px; border-radius:50%; background:var(--success); color:white;"><i class="fa-solid fa-plus"></i></button>
        </div>
        <div id="teacherList"></div>
    </div>
</div>

<div id="popup">
    <div class="popup-content">
        <h2 style="margin:0 0 25px 0;">New Faculty Member</h2>
        <input type="text" id="newTName" placeholder="Full Name">
        <input type="email" id="newTEmail" placeholder="Email Address">
        <input type="password" id="newTPass" placeholder="System Password">
        <button class="btn btn-ai-hub" style="width:100%; margin-top:10px;" onclick="createTeacher()">Save Teacher</button>
        <button class="btn" onclick="togglePopup(false)" style="width:100%; margin-top:5px; color: var(--text-dim);">Cancel</button>
    </div>
</div>

<script>
    const BASE_URL = '/api'; 
    let chartInstance = null;
    let totalTeacherCount = 0;

    const headers = () => ({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    });

    const handleRes = async (res) => {
        try { const json = await res.json(); return json; } 
        catch (e) { return { success: false, message: "Server error" }; }
    };

    document.addEventListener('DOMContentLoaded', init);

    async function init() {
        await refreshTeachers();
        await updateStatusSummary(); 
    }

    async function updateStatusSummary() {
        try {
            const res = await fetch(`${BASE_URL}/teachers-attendance-status`);
            const json = await res.json();
            if(json.success) {
                const absentCount = json.data.filter(t => t.current_status === 'absent').length;
                document.getElementById('total-absent').innerText = absentCount;
                document.getElementById('total-present').innerText = totalTeacherCount - absentCount;
            }
        } catch(e) { console.error("Stats Error", e); }
    }

    async function refreshTeachers() {
        const response = await fetch(`${BASE_URL}/teachers`);
        const json = await handleRes(response);
        if (!json || !json.success) return;
        
        totalTeacherCount = json.data.length;
        const list = json.data;
        
        // Update Side Panel ONLY with password field
        const sidebar = document.getElementById('teacherList');
        sidebar.innerHTML = list.map(t => `
            <div class="teacher-item glass-card">
                <div style="font-weight:700; margin-bottom: 10px; font-size:14px; color:var(--accent);">ID: ${t.id} - ${t.name}</div>
                <input type="text" value="${t.name}" id="edit-name-${t.id}">
                <input type="email" value="${t.email}" id="edit-email-${t.id}">
                <input type="password" id="edit-pass-${t.id}" placeholder="New Password (optional)">
                <div style="display:flex; gap:8px; margin-top: 10px;">
                    <button class="btn btn-ai-hub" style="flex: 1; padding: 10px;" onclick="updateTeacher(${t.id})">Update</button>
                    <button class="btn" style="background: rgba(244, 63, 94, 0.1); color: var(--danger); border: 1px solid var(--danger);" onclick="deleteTeacher(${t.id})"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>`).join('');
            
        // Update Header Dropdown
        const dropdown = document.getElementById('teacherDropdown');
        dropdown.innerHTML = list.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
        if (list.length > 0 && !dropdown.value) switchTeacher(list[0].id);
    }

    async function updateTeacher(id) {
        const name = document.getElementById(`edit-name-${id}`).value;
        const email = document.getElementById(`edit-email-${id}`).value;
        const password = document.getElementById(`edit-pass-${id}`).value;
        
        const payload = { name, email };
        if (password && password.trim() !== "") payload.password = password;

        const response = await fetch(`${BASE_URL}/teachers/${id}`, { 
            method: 'PUT', headers: headers(), body: JSON.stringify(payload) 
        });
        const result = await handleRes(response);
        if (result.success) { alert("Updated Successfully!"); refreshTeachers(); }
    }

    async function switchTeacher(id) {
        if(!id) return;
        document.getElementById('loader').style.display = 'block';
        try {
            const statsRes = await fetch(`${BASE_URL}/dashboard?teacher_id=${id}`);
            const stats = await handleRes(statsRes);
            const statsData = stats.success ? stats.data : stats;
            document.getElementById('stat-attendance').innerText = statsData.total_attendance || 0;
            document.getElementById('stat-weak').innerText = statsData.weak_topics || 0;
            document.getElementById('stat-ai').innerText = statsData.ai_generated || 0;

            const notifsRes = await fetch(`${BASE_URL}/notifications/${id}`);
            const notifsJson = await handleRes(notifsRes);
            const nList = document.getElementById('notificationList');
            const today = new Date().toISOString().split('T')[0];
            const allNotifs = Array.isArray(notifsJson) ? notifsJson : (notifsJson.data || []);
            const filteredNotifs = allNotifs.filter(n => !n.created_at || n.created_at.includes(today));

            if (filteredNotifs.length > 0) {
                nList.innerHTML = filteredNotifs.map(n => {
                    const targetPdf = n.pdf_url || n.debug_url;
                    const isAbs = n.type === 'absent';
                    return `<div class="glass-card" style="margin-bottom:12px; border-left:4px solid ${isAbs?'var(--danger)':'var(--accent)'}">
                        <h4 style="margin:0; font-size:15px;">${n.title}</h4>
                        <p style="margin:5px 0; font-size:13px; color:var(--text-dim);">${n.message}</p>
                        ${targetPdf ? `<a href="${targetPdf}" target="_blank" class="btn" style="background: var(--accent); color: white; padding: 5px 10px; font-size: 11px; margin-top: 5px;">VIEW PDF</a>` : ''}
                    </div>`;
                }).join('');
            } else { nList.innerHTML = '<p style="text-align:center; color:var(--text-dim);">No alerts today.</p>'; }
        } catch (e) { console.error(e); }
        document.getElementById('loader').style.display = 'none';
    }

    async function createTeacher() {
        const name = document.getElementById('newTName').value;
        const email = document.getElementById('newTEmail').value;
        const password = document.getElementById('newTPass').value;
        const res = await fetch(`${BASE_URL}/teachers`, { method: 'POST', headers: headers(), body: JSON.stringify({ name, email, password }) });
        const result = await handleRes(res);
        if (result.success) { togglePopup(false); refreshTeachers(); }
    }

    async function deleteTeacher(id) {
        if(!confirm("Delete this faculty?")) return;
        await fetch(`${BASE_URL}/teachers/${id}`, { method: 'DELETE', headers: headers() });
        refreshTeachers();
    }

    function togglePopup(show) { document.getElementById('popup').style.display = show ? 'flex' : 'none'; }
    async function fetchTeacherDetailedList(type) {
        const res = await fetch(`${BASE_URL}/teachers-attendance-status`);
        const json = await res.json();
        const area = document.getElementById('teacherDetailArea');
        area.style.display = 'block';
        const list = json.data.filter(t => type === 'absent' ? t.current_status === 'absent' : t.current_status !== 'absent');
        area.innerHTML = `<table style="width:100%; border-collapse:collapse;"><tbody>${list.map(t => `<tr style="border-bottom: 1px solid var(--glass-border);"><td style="padding:10px;">${t.name}</td><td style="text-align:right; color:${type==='absent'?'var(--danger)':'var(--success)'}">${type.toUpperCase()}</td></tr>`).join('')}</tbody></table>`;
    }
</script>
</body> 
</html>