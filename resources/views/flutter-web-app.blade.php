<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AI Hybrid App | Live</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background: #020617; font-family: 'Outfit', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: white; }
        .iphone { width: 375px; height: 780px; background: #0f172a; border: 10px solid #1e293b; border-radius: 45px; position: relative; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 50px 100px -20px rgba(0,0,0,0.7); }
        .screen { flex: 1; overflow-y: auto; padding: 20px; padding-bottom: 90px; }
        .screen::-webkit-scrollbar { display: none; }
        .glass { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(10px); border-radius: 24px; padding: 15px; }
        .input-style { width: 100%; background: #1e293b; border: 1px solid #334155; color: white; padding: 15px; border-radius: 15px; outline: none; margin-bottom: 12px; }
        .nav-bar { position: absolute; bottom: 15px; left: 15px; right: 15px; height: 65px; background: rgba(30, 41, 59, 0.9); backdrop-filter: blur(20px); border-radius: 25px; display: flex; justify-content: space-around; align-items: center; border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body>

    <div class="iphone">
        <div class="flex justify-between px-8 pt-4 pb-2 text-[12px] opacity-60">
            <span>9:41</span>
            <div class="flex gap-1.5"><i class="fas fa-signal"></i><i class="fas fa-wifi"></i><i class="fas fa-battery-full"></i></div>
        </div>

        <div class="screen" id="app-viewport">
            <div id="home-view">
                <div class="flex justify-between items-center mb-6 mt-2">
                    <h1 class="text-2xl font-black italic">AI HYBRID</h1>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/30"><i class="fas fa-bolt"></i></div>
                </div>

                <div class="glass mb-6">
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Dashboard Sync</p>
                    <h2 class="text-xl font-bold mt-1">Live Faculty Feed</h2>
                </div>

                <div class="grid gap-3">
                    <div onclick="changeView('register')" class="glass flex items-center gap-4 cursor-pointer hover:bg-white/5 transition-all">
                        <div class="w-12 h-12 bg-emerald-500/20 text-emerald-400 rounded-2xl flex items-center justify-center"><i class="fas fa-plus"></i></div>
                        <div><p class="font-bold text-sm">Student Registration</p><p class="text-[10px] text-slate-500">API: /students/register</p></div>
                    </div>

                    <div onclick="changeView('rating')" class="glass flex items-center gap-4 cursor-pointer hover:bg-white/5 transition-all">
                        <div class="w-12 h-12 bg-amber-500/20 text-amber-400 rounded-2xl flex items-center justify-center"><i class="fas fa-star"></i></div>
                        <div><p class="font-bold text-sm">Topic Rating</p><p class="text-[10px] text-slate-500">API: /topic-rating</p></div>
                    </div>

                    <div onclick="changeView('notifications')" class="glass flex items-center gap-4 cursor-pointer hover:bg-white/5 transition-all">
                        <div class="w-12 h-12 bg-indigo-500/20 text-indigo-400 rounded-2xl flex items-center justify-center"><i class="fas fa-bell"></i></div>
                        <div><p class="font-bold text-sm">Inbox</p><p class="text-[10px] text-slate-500">API: /notifications/{id}</p></div>
                    </div>
                </div>
            </div>

            <div id="register-view" class="hidden">
                <button onclick="changeView('home')" class="text-indigo-400 mb-4 font-bold"><i class="fas fa-chevron-left mr-1"></i> BACK</button>
                <h2 class="text-xl font-bold mb-6">New Student [cite: 24]</h2>
                <input type="text" id="reg-name" placeholder="Full Name" class="input-style">
                <input type="number" id="reg-class" placeholder="Classroom ID" class="input-style">
                <button onclick="registerStudent()" id="reg-btn" class="w-full bg-emerald-600 p-4 rounded-2xl font-bold shadow-lg shadow-emerald-500/20">CONFIRM REGISTER</button>
            </div>

            <div id="rating-view" class="hidden">
                <button onclick="changeView('home')" class="text-indigo-400 mb-4 font-bold"><i class="fas fa-chevron-left mr-1"></i> BACK</button>
                <h2 class="text-xl font-bold mb-6">Rate Topic [cite: 39]</h2>
                <input type="text" id="rate-topic" placeholder="Topic Name (e.g. Algebra)" class="input-style">
                <input type="number" id="rate-rating" placeholder="Rating (1-5)" class="input-style">
                <button onclick="submitRating()" id="rate-btn" class="w-full bg-amber-600 p-4 rounded-2xl font-bold shadow-lg shadow-amber-500/20">SUBMIT RATING</button>
            </div>

            <div id="notifications-view" class="hidden">
                <button onclick="changeView('home')" class="text-indigo-400 mb-4 font-bold"><i class="fas fa-chevron-left mr-1"></i> BACK</button>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">Faculty Inbox [cite: 51]</h2>
                    <button onclick="loadNotifications()" class="text-xs text-indigo-400 uppercase font-bold tracking-widest">Refresh</button>
                </div>
                <div id="notif-list" class="space-y-3">
                    <p class="text-slate-500 text-center py-10 italic">Checking for alerts...</p>
                </div>
            </div>
        </div>

        <div class="nav-bar">
            <div onclick="changeView('home')" class="text-indigo-400"><i class="fas fa-home"></i></div>
            <div onclick="changeView('notifications')"><i class="fas fa-comment-dots"></i></div>
            <div><i class="fas fa-compass"></i></div>
            <div><i class="fas fa-user-circle"></i></div>
        </div>
    </div>

    <script>
        const API_BASE = '/api'; // 

        function changeView(view) {
            document.getElementById('home-view').classList.add('hidden');
            document.getElementById('register-view').classList.add('hidden');
            document.getElementById('rating-view').classList.add('hidden');
            document.getElementById('notifications-view').classList.add('hidden');
            document.getElementById(view + '-view').classList.remove('hidden');
            if(view === 'notifications') loadNotifications();
        }

        async function registerStudent() {
            const name = document.getElementById('reg-name').value;
            const classroom_id = document.getElementById('reg-class').value;
            const btn = document.getElementById('reg-btn');

            if(!name || !classroom_id) return alert("Fields are empty!");
            btn.innerText = "Syncing with Model..."; // [cite: 27]

            const res = await fetch(`${API_BASE}/students/register`, { // [cite: 25]
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ name, classroom_id }) // [cite: 26]
            });

            const data = await res.json();
            if(data.success) {
                alert(`SUCCESS! ID Generated: ${data.student_id}. Use this to train AI Model.`); // [cite: 27]
                changeView('home');
            }
            btn.innerText = "CONFIRM REGISTER";
        }

        async function submitRating() {
            const topic_name = document.getElementById('rate-topic').value;
            const rating = document.getElementById('rate-rating').value;
            
            const res = await fetch(`${API_BASE}/topic-rating`, { // [cite: 40]
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ // [cite: 42]
                    teacher_id: 1, // Static for testing [cite: 43]
                    classroom_id: 1, 
                    subject_id: 1,
                    topic_name: topic_name, // [cite: 46]
                    rating: rating // [cite: 47]
                })
            });

            const data = await res.json();
            if(data.success) {
                alert("Topic Saved! Notification will be sent to Dashboard.");
                changeView('home');
            }
        }

        async function loadNotifications() {
            const list = document.getElementById('notif-list');
            const res = await fetch(`${API_BASE}/notifications/1`); // Using Teacher ID 1 [cite: 52]
            const data = await res.json();

            if(data.success && data.data.length > 0) {
                list.innerHTML = data.data.map(n => `
                    <div class="glass flex items-start justify-between">
                        <div>
                            <p class="font-bold text-sm">${n.title}</p>
                            <p class="text-[10px] text-slate-400 mt-1">${n.message}</p>
                        </div>
                        <button onclick="markDone(${n.id}, this)" class="text-indigo-400 text-xs font-black">DONE [cite: 53]</button>
                    </div>
                `).join('');
            } else {
                list.innerHTML = `<p class="text-slate-500 text-center py-10">Inbox is empty!</p>`;
            }
        }

        async function markDone(id, el) {
            el.innerText = "...";
            const res = await fetch(`${API_BASE}/notifications/done/${id}`, { method: 'POST' }); // [cite: 54]
            const data = await res.json();
            if(data.success) el.parentElement.remove();
        }
    </script>
</body>
</html>