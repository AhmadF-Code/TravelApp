<x-filament-panels::page>
@php
    $ana = $this->analytics;
@endphp

<div class="ana-root">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .fi-page-header { display: none !important; }
    .ana-root { font-family: 'Inter', sans-serif; color: #1e293b; }
    .ana-card { background: white; border-radius: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; height: 100%; }
    .ana-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f8fafc; display: flex; align-items: center; justify-content: space-between; }
    .ana-card-title { font-size: 0.875rem; font-weight: 800; color: #0f172a; margin: 0; }
    .ana-card-body { padding: 1.5rem; }
    
    .ana-hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 1.5rem; padding: 2rem 2.5rem; color: white; margin-bottom: 1.5rem; position: relative; overflow: hidden; }
    .ana-hero h1 { font-size: 1.875rem; font-weight: 900; margin-bottom: 0.5rem; }
    .ana-hero p { color: #94a3b8; font-size: 0.875rem; max-width: 480px; }
    
    .ana-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem; }
    .ana-stat-card { padding: 1.25rem; }
    .ana-stat-lbl { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin-bottom: 0.4rem; }
    .ana-stat-val { font-size: 1.5rem; font-weight: 900; color: #0f172a; }
    
    .funnel-step { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; position: relative; }
    .funnel-bar-bg { flex: 1; height: 2.5rem; background: #f1f5f9; border-radius: 0.6rem; margin: 0 1rem; position: relative; overflow: hidden; }
    .funnel-bar-fill { height: 100%; background: #3b82f6; border-radius: 0.6rem; transition: width 1s ease-out; }
    .funnel-lbl { width: 120px; font-size: 0.75rem; font-weight: 700; color: #475569; }
    .funnel-val { width: 60px; text-align: right; font-size: 0.875rem; font-weight: 900; color: #0f172a; }

    .ana-table { width: 100%; font-size: 0.8rem; }
    .ana-table th { text-align: left; padding: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; font-size: 0.65rem; }
    .ana-table td { padding: 0.75rem; border-top: 1px solid #f8fafc; }

    @media (max-width: 1024px) { .ana-grid-4 { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .ana-grid-4 { grid-template-columns: 1fr; } }
</style>

<div class="ana-hero">
    <div style="position:relative; z-index:2">
        <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.65rem; font-weight:900; text-transform:uppercase; color:#34d399; margin-bottom:0.75rem">
            <span style="width:8px; height:8px; border-radius:50%; background:#34d399; display:inline-block"></span>
            Landing Performance active
        </div>
        <h1>Marketing & Traffic Insights</h1>
        <form method="GET" style="margin-top:1.5rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap">
            <input type="date" name="start" value="{{ $this->startDate }}" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:0.75rem; padding:0.5rem 1rem; color:white; font-size:0.8rem; outline:none">
            <span style="color:#64748b">—</span>
            <input type="date" name="end" value="{{ $this->endDate }}" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:0.75rem; padding:0.5rem 1rem; color:white; font-size:0.8rem; outline:none">
            <button type="submit" style="background:#3b82f6; color:white; border:none; padding:0.55rem 1.5rem; border-radius:0.75rem; font-weight:800; font-size:0.8rem; cursor:pointer">Sync</button>
        </form>
    </div>
</div>

<div class="ana-grid-4">
    <div class="ana-card ana-stat-card">
        <div class="ana-stat-lbl">Unique Visitors</div>
        <div class="ana-stat-val">{{ number_format($ana['unique_sessions']) }}</div>
    </div>
    <div class="ana-card ana-stat-card">
        <div class="ana-stat-lbl">Page Views</div>
        <div class="ana-stat-val">{{ number_format($ana['total_pv']) }}</div>
    </div>
    <div class="ana-card ana-stat-card">
        <div class="ana-stat-lbl">CTA Conversions</div>
        <div class="ana-stat-val">{{ number_format($ana['funnel']['cta_clicks']) }}</div>
    </div>
    <div class="ana-card ana-stat-card">
        <div class="ana-stat-lbl">Convertion Rate</div>
        <div class="ana-stat-val">{{ $ana['funnel']['visitors'] > 0 ? round(($ana['paid_count'] / $ana['funnel']['visitors']) * 100, 2) : 0 }}%</div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:1.25rem; margin-bottom:1.5rem">
    <div class="ana-card">
        <div class="ana-card-header"><h3 class="ana-card-title">Daily Trend Chart</h3></div>
        <div class="ana-card-body">
            <canvas id="trafficChart" style="max-height:220px"></canvas>
        </div>
    </div>
    <div class="ana-card">
        <div class="ana-card-header"><h3 class="ana-card-title">Segments</h3></div>
        <div class="ana-card-body">
             <div style="display:flex; justify-content:center; gap:2rem">
                 <div style="width:120px; text-align:center">
                     <canvas id="deviceChart"></canvas>
                     <div style="font-size:0.6rem; font-weight:800; margin-top:0.5rem; color:#64748b">DEVICES</div>
                 </div>
                 <div style="width:120px; text-align:center">
                     <canvas id="sourceChart"></canvas>
                     <div style="font-size:0.6rem; font-weight:800; margin-top:0.5rem; color:#64748b">SOURCES</div>
                 </div>
             </div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.25rem">
    <div class="ana-card">
        <div class="ana-card-header"><h3 class="ana-card-title">Marketing Funnel</h3></div>
        <div class="ana-card-body">
             @php
                $v = $ana['funnel']['visitors'] ?: 1;
                $steps = [
                    ['lbl' => 'Total Visitors', 'val' => $ana['funnel']['visitors'], 'pct' => 100, 'clr' => '#94a3b8'],
                    ['lbl' => 'CTA Interaction', 'val' => $ana['funnel']['cta_clicks'], 'pct' => ($ana['funnel']['cta_clicks']/$v)*100, 'clr' => '#3b82f6'],
                    ['lbl' => 'New Booking', 'val' => $ana['funnel']['bookings'], 'pct' => ($ana['funnel']['bookings']/$v)*100, 'clr' => '#f59e0b'],
                    ['lbl' => 'Paid (Final)', 'val' => $ana['funnel']['paid'], 'pct' => ($ana['funnel']['paid']/$v)*100, 'clr' => '#10b981'],
                ];
            @endphp
            @foreach($steps as $step)
                <div class="funnel-step">
                    <div class="funnel-lbl">{{ $step['lbl'] }}</div>
                    <div class="funnel-bar-bg">
                        <div class="funnel-bar-fill" style="width: {{ $step['pct'] }}%; background: {{ $step['clr'] }}"></div>
                    </div>
                    <div class="funnel-val">{{ $step['val'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="ana-card">
        <div class="ana-card-header"><h3 class="ana-card-title">Top Locations</h3></div>
        <table class="ana-table">
            <thead>
                <tr><th>City</th><th>Visitors</th><th>Reach</th></tr>
            </thead>
            <tbody>
                @foreach($ana['top_cities'] as $city)
                <tr>
                    <td style="font-weight:700">{{ $city->city ?: 'Unknown' }}</td>
                    <td style="font-weight:800">{{ number_format($city->count) }}</td>
                    <td>
                        <div style="height:4px; background:#f1f5f9; border-radius:2px">
                             <div style="height:100%; background:#3b82f6; border-radius:2px; width:{{ ($city->count / ($ana['top_cities']->max('count') ?: 1)) * 100 }}%"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tfCtx = document.getElementById('trafficChart').getContext('2d');
        new Chart(tfCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($ana['traffic_trend']->pluck('date')) !!},
                datasets: [{
                    label: 'Daily Visitors',
                    data: {!! json_encode($ana['traffic_trend']->pluck('count')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } } }
        });

        new Chart(document.getElementById('deviceChart'), {
            type: 'doughnut',
            data: { labels: {!! json_encode($ana['devices']->pluck('device_type')) !!}, datasets: [{ data: {!! json_encode($ana['devices']->pluck('count')) !!}, backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'], borderWidth: 0 }] },
            options: { cutout: '75%', plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('sourceChart'), {
            type: 'doughnut',
            data: { labels: {!! json_encode($ana['sources']->pluck('source')) !!}, datasets: [{ data: {!! json_encode($ana['sources']->pluck('count')) !!}, backgroundColor: ['#8b5cf6', '#ec4899', '#f97316'], borderWidth: 0 }] },
            options: { cutout: '75%', plugins: { legend: { display: false } } }
        });
    });
</script>

</div>
</x-filament-panels::page>
