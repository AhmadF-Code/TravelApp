<x-filament-panels::page>
<div class="cal-root-v3" wire:ignore.self>
<style>
    .cal-root-v3 { font-family: 'Inter', -apple-system, sans-serif; }
    .cal-legend { display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; margin-bottom: 1.25rem; padding: 0.875rem 1.25rem; background: white; border-radius: 0.875rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .cal-legend-dot { width: 0.75rem; height: 0.75rem; border-radius: 3px; flex-shrink: 0; }
    .cal-card { background: white; border-radius: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(0,0,0,0.05); padding: 1.5rem; }
    .fc .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 900 !important; color: #0f172a !important; }
    .fc .fc-button-primary { background: #f8fafc !important; color: #475569 !important; border: 1.5px solid #e2e8f0 !important; }
    .cal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.5); backdrop-filter: blur(4px); z-index: 9998; display: none; }
    .cal-overlay.open { display: block; }
    .cal-modal { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; width: 100%; max-width: 30rem; background: white; border-radius: 1.5rem; box-shadow: 0 25px 60px rgba(0,0,0,0.25); display: none; }
    .cal-modal.open { display: block; }
    .cal-btn-red { background: #fff1f2; color: #dc2626; border: 1.5px solid #fecaca; padding: 0.75rem; border-radius: 0.75rem; cursor: pointer; flex: 1; font-weight: 700; }
    .cal-btn-dark { background: #0f172a; color: white; padding: 0.75rem; border-radius: 0.75rem; text-decoration: none; flex: 1; text-align: center; font-weight: 700; }
</style>

<div class="cal-legend">
    <span style="font-size:0.8rem;font-weight:900;color:#0f172a;margin-right:auto">Kalender Operasional Trip</span>
    <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.7rem;font-weight:700;color:#475569"><div class="cal-legend-dot" style="background:#3b82f6"></div>Akan Datang</div>
    <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.7rem;font-weight:700;color:#475569"><div class="cal-legend-dot" style="background:#10b981"></div>Selesai</div>
    <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.7rem;font-weight:700;color:#475569"><div class="cal-legend-dot" style="background:#f43f5e"></div>Batal</div>
</div>

<div class="cal-card">
    <div id="calendar" style="min-height:680px"></div>
</div>

<div class="cal-overlay" id="cal-overlay" onclick="closeCalModal()"></div>

<div class="cal-modal" id="cal-modal">
    <div id="cal-modal-header" style="padding:1.5rem 2rem;border-radius:1.5rem 1.5rem 0 0;position:relative;background:linear-gradient(135deg,#1e3a5f,#2563eb)">
        <button onclick="closeCalModal()" style="position:absolute;top:1rem;right:1rem;color:white;background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:2rem;height:2rem;cursor:pointer">✕</button>
        <div id="cal-modal-trip-name" style="font-size:1.25rem;font-weight:900;color:white">Trip Name</div>
        <div id="cal-modal-dates" style="font-size:0.75rem;color:rgba(255,255,255,0.8);font-weight:700;margin-top:0.25rem">Dates</div>
    </div>
    <div style="padding:2rem">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-bottom:1.5rem">
            <div style="background:#f0fdf4;padding:1rem;text-align:center;border-radius:1rem"><div id="cal-stat-paid" style="font-size:1.5rem;font-weight:900;color:#16a34a">0</div><div style="font-size:0.6rem;font-weight:800;color:#16a34a;text-transform:uppercase">Lunas</div></div>
            <div style="background:#fffbeb;padding:1rem;text-align:center;border-radius:1rem"><div id="cal-stat-pending" style="font-size:1.5rem;font-weight:900;color:#d97706">0</div><div style="font-size:0.6rem;font-weight:800;color:#d97706;text-transform:uppercase">Pending</div></div>
            <div style="background:#fff1f2;padding:1rem;text-align:center;border-radius:1rem"><div id="cal-stat-cancel" style="font-size:1.5rem;font-weight:900;color:#dc2626">0</div><div style="font-size:0.6rem;font-weight:800;color:#dc2626;text-transform:uppercase">Batal</div></div>
        </div>
        <div style="display:flex;gap:1rem">
            <a href="#" id="cal-edit-btn" class="cal-btn-dark">Edit Jadwal</a>
            <button id="cal-cancel-btn" onclick="confirmCancelTrip()" class="cal-btn-red">Batal Trip</button>
        </div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>
<script>
    var _sid = null;
    function openCalModal(p) {
        _sid = p.id;
        document.getElementById('cal-modal-trip-name').textContent = p.trip_name;
        document.getElementById('cal-modal-dates').textContent = p.dates;
        document.getElementById('cal-stat-paid').textContent = p.pax_paid;
        document.getElementById('cal-stat-pending').textContent = p.pax_pending;
        document.getElementById('cal-stat-cancel').textContent = p.pax_cancelled;
        document.getElementById('cal-edit-btn').href = '/admin/schedules/' + p.id + '/edit';
        document.getElementById('cal-cancel-btn').style.display = (p.status === 'active') ? 'block' : 'none';
        document.getElementById('cal-overlay').classList.add('open');
        document.getElementById('cal-modal').classList.add('open');
    }
    function closeCalModal() {
        document.getElementById('cal-overlay').classList.remove('open');
        document.getElementById('cal-modal').classList.remove('open');
    }
    async function confirmCancelTrip() {
        if(!confirm('Batalkan jadwal?')) return;
        const r = await fetch('/admin/schedules/cancel/' + _sid, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        if(r.ok) window.location.reload();
    }
    document.addEventListener('DOMContentLoaded', function() {
        var cal = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView:'dayGridMonth', locale:'id', events:@json($schedules),
            eventClick:function(i){ openCalModal(i.event.extendedProps); },
            eventContent:function(a){
                return { html:'<div style="padding:4px;font-size:10px;font-weight:800">'+a.event.title+'<br><span style="opacity:0.7;font-weight:400">○ '+a.event.extendedProps.remaining+' slot</span></div>' };
            }
        });
        cal.render();
    });
</script>
</div>
</x-filament-panels::page>
