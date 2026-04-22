<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Analytics | AI Hybrid</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .active-box { 
            border-color: #6366f1; 
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(79, 70, 229, 0.2) 100%);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
            color: #818cf8;
        }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.2); }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-500/50">📊</span>
                    Student Analytics
                </h1>
                <p class="text-slate-400 mt-1">Class-wise daily attendance insights</p>
            </div>
            <a href="{{ url('/dashboard') }}" class="px-4 py-2 glass rounded-xl text-sm hover:bg-slate-800 transition-all">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="mb-8">
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3 block ml-1">Select Classroom</p>
            <div class="flex gap-4 overflow-x-auto hide-scrollbar pb-2">
                @foreach($classrooms as $class)
                <div onclick="selectClass({{ $class->id }}, this)" 
                     class="class-item flex-shrink-0 px-8 py-4 glass rounded-2xl cursor-pointer transition-all hover:scale-105 active:scale-95 text-center min-w-[140px]">
                    <span class="block text-lg font-bold">{{ $class->name }}</span>
                    <span class="text-[10px] opacity-50 uppercase">Room {{ $class->id }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mb-10">
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3 block ml-1">Select Date (April)</p>
            <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-2">
                @for($i = 1; $i <= 30; $i++)
                @php $dateStr = "2026-04-" . str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                <div onclick="selectDate('{{ $dateStr }}', this)" 
                     class="date-item flex-shrink-0 w-16 h-20 glass rounded-2xl flex flex-col items-center justify-center cursor-pointer transition-all hover:border-indigo-500">
                    <span class="text-[10px] opacity-50 uppercase">Apr</span>
                    <span class="text-xl font-black">{{ $i }}</span>
                </div>
                @endfor
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="glass p-6 rounded-3xl stat-card relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-indigo-500/10 text-6xl font-black">TOT</div>
                <p class="text-slate-400 text-sm font-medium">Total Capacity</p>
                <h2 id="stat-total" class="text-4xl font-black mt-2 text-white">0</h2>
            </div>
            <div class="glass p-6 rounded-3xl stat-card border-l-4 border-l-emerald-500 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-emerald-500/10 text-6xl font-black">PRE</div>
                <p class="text-slate-400 text-sm font-medium text-emerald-400">Present Today</p>
                <h2 id="stat-present" class="text-4xl font-black mt-2 text-white">0</h2>
            </div>
            <div class="glass p-6 rounded-3xl stat-card border-l-4 border-l-rose-500 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-rose-500/10 text-6xl font-black">ABS</div>
                <p class="text-slate-400 text-sm font-medium text-rose-400">Absent Count</p>
                <h2 id="stat-absent" class="text-4xl font-black mt-2 text-white">0</h2>
            </div>
        </div>

        <div class="glass rounded-[2rem] overflow-hidden">
            <div class="p-6 border-b border-white/5 bg-white/5 flex justify-between items-center">
                <h3 class="font-bold text-xl tracking-tight text-white">Verified Students List</h3>
                <span id="list-count" class="bg-indigo-500/20 text-indigo-400 text-xs px-3 py-1 rounded-full border border-indigo-500/30">0 Present</span>
            </div>
            <ul id="student-list" class="grid grid-cols-1 md:grid-cols-2 gap-px bg-white/5">
                <li class="p-10 text-center text-slate-500 col-span-2 italic">Select class and date to scan data...</li>
            </ul>
        </div>
    </div>

    <script>
        let selectedClassId = null;
        let selectedDate = "{{ date('Y-m-d') }}";

        function selectClass(id, element) {
            document.querySelectorAll('.class-item').forEach(el => el.classList.remove('active-box'));
            element.classList.add('active-box');
            selectedClassId = id;
            fetchReport();
        }

        function selectDate(date, element) {
            document.querySelectorAll('.date-item').forEach(el => el.classList.remove('active-box'));
            element.classList.add('active-box');
            selectedDate = date;
            fetchReport();
        }

        async function fetchReport() {
            if (!selectedClassId || !selectedDate) return;
            
            const listEl = document.getElementById('student-list');
            listEl.innerHTML = '<div class="p-10 text-center col-span-2 text-indigo-400 animate-pulse">Loading real-time data...</div>';

            try {
                const response = await fetch(`/api/analytics/class-report?class_id=${selectedClassId}&date=${selectedDate}`);
                const res = await response.json();

                if (res.success) {
                    document.getElementById('stat-total').innerText = res.summary.total;
                    document.getElementById('stat-present').innerText = res.summary.present;
                    document.getElementById('stat-absent').innerText = res.summary.absent;
                    document.getElementById('list-count').innerText = `${res.summary.present} Verified`;

                    let listHtml = '';
                    if (res.students.length > 0) {
                        res.students.forEach(student => {
                            listHtml += `
                                <li class="p-5 flex items-center justify-between bg-[#1e293b] hover:bg-slate-700/50 transition-all border-b border-white/5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center font-bold text-white shadow-lg">
                                            ${student.id}
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg text-white">${student.name}</p>
                                            <p class="text-[10px] text-slate-500 flex items-center gap-1 uppercase tracking-widest">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span> AI Confirmed
                                            </p>
                                        </div>
                                    </div>
                                    <div class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-[10px] rounded-lg font-bold uppercase">Present</div>
                                </li>`;
                        });
                    } else {
                        listHtml = '<div class="p-10 text-center col-span-2 text-slate-500 italic">No attendance data found for this selection.</div>';
                    }
                    listEl.innerHTML = listHtml;
                }
            } catch (error) {
                console.error("Error:", error);
            }
        }
    </script>
</body>
</html>