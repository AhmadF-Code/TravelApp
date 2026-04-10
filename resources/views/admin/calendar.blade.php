<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Keberangkatan — Travel Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 800 !important; color: #0f172a; }
        .fc-button { border-radius: 0.5rem !important; font-weight: 700 !important; font-size: 0.75rem !important; }
        .fc-day-today { background: #fffbeb !important; }
        .cal-modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:100; }
        .cal-modal { display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:101; width:95%; max-width:480px; background:white; border-radius:1.25rem; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); }
    </style>
</head>
<body class="min-h-screen">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar Dummy to keep consistent feel --}}
        <div class="hidden lg:flex w-64 bg-slate-900 flex-col">
            <div class="p-6">
                <span class="text-white text-xl font-black uppercase">Travel<span class="text-amber-500">CMS</span></span>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="/admin" class="flex items-center gap-3 text-slate-400 hover:text-white px-4 py-3 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 text-white bg-slate-800 px-4 py-3 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Calendar
                </a>
            </nav>
            <div class="p-6 border-t border-slate-800">
                <a href="/admin" class="text-xs font-bold text-slate-500 hover:text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Admin
                </a>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col h-screen">
            <header class="bg-white border-b h-16 flex items-center justify-between px-8">
                <h2 class="font-black text-slate-800 text-lg">Operational Calendar</h2>
                <div class="flex items-center gap-4">
                    <span class="text-xs font-bold text-slate-400">Viewing as {{ auth()->user()->name }}</span>
                    <a href="/admin" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-slate-200 transition">Dashboard</a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    <div id="calendar" class="min-h-[700px]"></div>
                </div>
            </main>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="cal-modal-overlay" id="overlay" onclick="closeModal()"></div>
    <div class="cal-modal" id="modal">
        <div id="m-header" class="p-6 rounded-t-xl relative overflow-hidden bg-slate-900">
            <div class="relative z-10">
                <button onclick="closeModal()" class="absolute top-0 right-0 text-white/50 hover:text-white">✕</button>
                <div id="m-title" class="text-xl font-black text-white leading-tight">Trip Title</div>
                <div id="m-dates" class="text-xs font-bold text-amber-500 mt-1 uppercase opacity-80">Dates</div>
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-3 gap-3 mb-8">
                <div class="bg-emerald-50 p-4 rounded-xl text-center">
                    <div id="m-paid" class="text-xl font-black text-emerald-600">0</div>
                    <div class="text-[10px] uppercase font-black text-emerald-500 mt-1">Paid</div>
                </div>
                <div class="bg-amber-50 p-4 rounded-xl text-center">
                    <div id="m-pending" class="text-xl font-black text-amber-600">0</div>
                    <div class="text-[10px] uppercase font-black text-amber-500 mt-1">Pending</div>
                </div>
                <div class="bg-rose-50 p-4 rounded-xl text-center">
                    <div id="m-cancel" class="text-xl font-black text-rose-600">0</div>
                    <div class="text-[10px] uppercase font-black text-rose-500 mt-1">Cancel</div>
                </div>
            </div>
            
            <div class="flex gap-3">
                <a href="#" id="m-edit" class="flex-1 bg-slate-900 text-white py-3 rounded-xl font-bold text-center text-sm hover:bg-slate-800 transition">Manage Schedule</a>
                <button id="m-btn-cancel" onclick="cancelTrip()" class="flex-1 bg-rose-50 text-rose-600 py-3 rounded-xl font-bold text-sm border border-rose-100 hover:bg-rose-100 transition">Cancel Trip</button>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>
    <script>
        var currentId = null;
        function openModal(p) {
            currentId = p.id;
            document.getElementById('m-title').textContent = p.trip_name;
            document.getElementById('m-dates').textContent = p.dates;
            document.getElementById('m-paid').textContent = p.pax_paid;
            document.getElementById('m-pending').textContent = p.pax_pending;
            document.getElementById('m-cancel').textContent = p.pax_cancelled;
            document.getElementById('m-edit').href = '/admin/schedules/' + p.id + '/edit';
            document.getElementById('m-btn-cancel').style.display = (p.status === 'active') ? 'block' : 'none';
            document.getElementById('modal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
        async function cancelTrip() {
            if(!confirm('Batalkan trip ini? Pelanggan akan masuk daftar follow-up.')) return;
            const res = await fetch('/admin/schedules/cancel/' + currentId, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if(res.ok) window.location.reload();
        }
        document.addEventListener('DOMContentLoaded', function() {
            var cal = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'id',
                events: @json($schedules),
                eventClick: function(i) { openModal(i.event.extendedProps); },
                eventContent: function(a) {
                    return { html: '<div class="p-1 px-2"><div class="text-[10px] font-black truncate uppercase leading-tight">' + a.event.title + '</div><div class="text-[9px] opacity-70">' + a.event.extendedProps.remaining + ' slot left</div></div>' };
                }
            });
            cal.render();
        });
    </script>
</body>
</html>
