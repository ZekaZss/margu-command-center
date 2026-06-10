<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARGU - Advanced Command Center</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* SCROLLBAR AESTHETIC */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(5, 11, 20, 0.5); }
        ::-webkit-scrollbar-thumb { background: rgba(56, 189, 248, 0.3); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #38bdf8; }

        body { background-color: #050b14; color: #38bdf8; font-family: 'Rajdhani', sans-serif; margin: 0; overflow: hidden; }
        
        /* STARTUP LOADING SCREEN */
        #startup-loader { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #050b14; z-index: 9999; display: flex; flex-direction: column; justify-content: center; align-items: center; transition: opacity 0.8s ease-out, visibility 0.8s; }
        .radar-spinner { width: 80px; height: 80px; border: 4px solid rgba(56, 189, 248, 0.1); border-top-color: #38bdf8; border-bottom-color: #38bdf8; border-radius: 50%; animation: spin 1.2s cubic-bezier(0.5, 0.1, 0.5, 0.9) infinite; }
        .loader-text { margin-top: 25px; color: #38bdf8; font-weight: 700; letter-spacing: 4px; font-size: 1.2rem; animation: text-pulse 1.5s infinite; text-shadow: 0 0 10px rgba(56, 189, 248, 0.5); }
        .loader-subtext { color: #64748b; font-size: 0.8rem; letter-spacing: 2px; margin-top: 5px; }
        
        @keyframes spin { 100% { transform: rotate(360deg); } }
        @keyframes text-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        #radarMap { position: absolute; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 1; background-color: #050b14; }
        .hud-panel { background: rgba(5, 11, 20, 0.65); backdrop-filter: blur(12px); border: 1px solid rgba(56, 189, 248, 0.2); box-shadow: 0 0 20px rgba(0, 0, 0, 0.8), inset 0 0 15px rgba(56, 189, 248, 0.05); border-radius: 8px; z-index: 10; position: relative; }
        .sidebar-hud { position: absolute; top: 20px; left: 20px; width: 250px; height: calc(100vh - 40px); display: flex; flex-direction: column; }
        .brand-title { color: #fff; text-shadow: 0 0 10px #38bdf8; letter-spacing: 2px; font-weight: 700; font-size: 1.5rem; border-bottom: 1px solid rgba(56, 189, 248, 0.3); padding-bottom: 15px; margin-bottom: 20px; }
        
        .nav-btn { background: transparent; color: #94a3b8; border: 1px solid transparent; padding: 12px 15px; margin-bottom: 10px; text-align: left; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s ease; cursor: pointer; border-radius: 4px; }
        .nav-btn:hover, .nav-btn.active { background: rgba(56, 189, 248, 0.1); color: #fff; border-left: 3px solid #38bdf8; border-right: 1px solid rgba(56, 189, 248, 0.3); box-shadow: inset 10px 0 15px -10px #38bdf8; }
        
        .right-hud { position: absolute; top: 20px; right: 20px; width: 320px; height: calc(100vh - 40px); }
        .hud-header { font-size: 0.9rem; color: #7dd3fc; text-transform: uppercase; border-bottom: 1px dashed rgba(56, 189, 248, 0.3); padding-bottom: 5px; margin-bottom: 10px; }
        .log-entry { font-size: 0.85rem; margin-bottom: 8px; padding-left: 10px; border-left: 2px solid #38bdf8; }
        .log-time { color: #94a3b8; font-size: 0.75rem; }
        .top-metrics { position: absolute; top: 20px; left: 290px; right: 360px; display: flex; gap: 15px; z-index: 10; }
        .metric-box { flex: 1; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; }
        .metric-value { font-size: 1.8rem; font-weight: 700; color: #fff; text-shadow: 0 0 10px rgba(255, 255, 255, 0.5); }
        
        .sos-container { position: relative; width: 40px; height: 40px; }
        .sos-blinker { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 2px solid #ef4444; border-radius: 50%; animation: radar-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; }
        .sos-core { position: absolute; top: 50%; left: 50%; width: 12px; height: 12px; background-color: #ef4444; border-radius: 50%; transform: translate(-50%, -50%); border: 2px solid #fff; box-shadow: 0 0 15px #ef4444; }
        
        .unknown-blinker { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 2px dashed #f59e0b; border-radius: 50%; animation: radar-ping 1s infinite; }
        .unknown-core { position: absolute; top: 50%; left: 50%; width: 12px; height: 12px; background-color: #f59e0b; border-radius: 50%; transform: translate(-50%, -50%); border: 2px solid #fff; box-shadow: 0 0 15px #f59e0b; }
        .secure-core { background-color: #38bdf8; width: 10px; height: 10px; border-radius: 50%; box-shadow: 0 0 10px #38bdf8; border: 2px solid #fff; }
        
        @keyframes radar-ping { 0% { transform: scale(0.5); opacity: 1; } 100% { transform: scale(3); opacity: 0; border-width: 1px; } }
        @keyframes hud-blink { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.1); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes popupFadeIn { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }

        .leaflet-control-zoom { display: none; }
        
        .leaflet-popup { animation: popupFadeIn 0.3s ease-out; transition: opacity 0.3s ease, transform 0.3s ease; pointer-events: none; }
        .leaflet-popup-content-wrapper { background: rgba(5, 11, 20, 0.95); backdrop-filter: blur(8px); border: 1px solid #38bdf8; color: #fff; font-family: 'Rajdhani', sans-serif; border-radius: 4px; box-shadow: 0 0 20px rgba(56, 189, 248, 0.5), inset 0 0 15px rgba(0, 0, 0, 0.8); padding: 5px; }
        .leaflet-popup-tip { background: rgba(5, 11, 20, 0.95); border: 1px solid #38bdf8; box-shadow: 0 0 10px rgba(56, 189, 248, 0.3); }

        .leaflet-popup.sos-popup .leaflet-popup-content-wrapper { border-color: #ef4444; box-shadow: 0 0 20px rgba(239, 68, 68, 0.7), inset 0 0 15px rgba(0, 0, 0, 0.8); }
        .leaflet-popup.sos-popup .leaflet-popup-tip { border-color: #ef4444; }
        .leaflet-popup.unknown-popup .leaflet-popup-content-wrapper { border-color: #f59e0b; box-shadow: 0 0 20px rgba(245, 158, 11, 0.7), inset 0 0 15px rgba(0, 0, 0, 0.8); }
        .leaflet-popup.unknown-popup .leaflet-popup-tip { border-color: #f59e0b; }

        .popup-header { border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 5px; margin-bottom: 8px; font-weight: 700; }
        .popup-data-row { font-size: 0.9rem; display: flex; justify-content: space-between; margin-bottom: 4px; gap: 15px;}
        .popup-label { color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem; }

        #audio-toggle { position: absolute; bottom: 20px; left: 290px; background: rgba(5, 11, 20, 0.8); border: 1px solid #38bdf8; color: #38bdf8; padding: 8px 15px; border-radius: 4px; cursor: pointer; z-index: 10; font-family: 'Rajdhani', sans-serif; font-weight: bold; letter-spacing: 1px; transition: 0.3s; }
        #audio-toggle:hover { background: rgba(56, 189, 248, 0.2); }
        #audio-toggle.active { color: #ef4444; border-color: #ef4444; }
        
        .modal-content-hud { background: rgba(5, 11, 20, 0.95) !important; backdrop-filter: blur(20px); border: 2px solid #38bdf8 !important; box-shadow: 0 0 30px rgba(56, 189, 248, 0.3); color: #38bdf8; }
        .modal-header-hud { border-bottom: 1px dashed rgba(56, 189, 248, 0.3); }
        .form-control-hud { background: rgba(30, 41, 59, 0.5) !important; border: 1px solid rgba(56, 189, 248, 0.3) !important; color: #fff !important; }
        .form-control-hud:focus { border-color: #38bdf8 !important; box-shadow: 0 0 10px rgba(56, 189, 248, 0.5) !important; }

        /* TABEL DIREKTORI (Pemisahan Kolom) */
        .table-hud { color: #fff; vertical-align: middle; font-size: 0.9rem; }
        .table-hud th { border-bottom: 1px solid rgba(56, 189, 248, 0.5); font-weight: 600; text-transform: uppercase; color: #94a3b8; }
        .table-hud td { border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding: 10px 5px; }
        .table-patrol th { border-color: rgba(56, 189, 248, 0.5); }
        .table-civilian th { border-color: rgba(16, 185, 129, 0.5); }
    </style>
</head>
<body>

<div id="startup-loader">
    <div class="radar-spinner"></div>
    <div class="loader-text">MARGU COMMAND CENTER</div>
    <div class="loader-subtext">INITIALIZING SATELLITE LINK...</div>
</div>

<div id="radarMap"></div>
<button id="audio-toggle">🔇 ALARM SYSTEM: STANDBY</button>

<div class="hud-panel sidebar-hud p-4">
    <div class="text-center brand-title">
        ⚓ MARGU<br>
        <span style="font-size: 0.8rem; color: #94a3b8; letter-spacing: 3px; font-weight: 400;">SYS-COM V1.0</span>
    </div>
    
    <button id="btn-live" class="nav-btn active">🛰️ Live Tracking</button>
    <button id="btn-emergency" class="nav-btn" style="color: #ef4444; border-right-color: #ef4444;">
        🚨 Emergency Alerts <span id="hud-sos-badge" class="badge bg-danger float-end mt-1">{{ $sosCount }}</span>
    </button>
    <button id="btn-patrol" class="nav-btn">⛴️ Patrol Units</button>
    <button id="btn-directory" class="nav-btn" data-bs-toggle="modal" data-bs-target="#directoryModal">📋 Device Directory</button>
    <button id="btn-analytics" class="nav-btn" data-bs-toggle="modal" data-bs-target="#analyticsModal">📊 System Analytics</button>
    
    <button class="nav-btn mt-4 text-warning" onclick="simulateUnknownDevice()" style="border-top: 1px dashed #f59e0b;">⚠️ TEST ALIEN SIGNAL</button>
    
    <button class="nav-btn mt-auto" data-bs-toggle="modal" data-bs-target="#registerModal" style="border-top: 1px solid rgba(56, 189, 248, 0.3); color: #fff;">⚙️ BIND DEVICE</button>
</div>

<div class="top-metrics">
    <div class="hud-panel metric-box">
        <div>
            <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Active DB Signals</div>
            <div id="hud-total-signals" class="metric-value">{{ $totalSignals }}</div>
        </div>
        <div style="color: #10b981; font-size: 2rem;">🌊</div>
    </div>
    <div class="hud-panel metric-box" id="sos-panel-box" style="border-color: rgba(239, 68, 68, 0.5); box-shadow: inset 0 0 20px rgba(239, 68, 68, 0.1);">
        <div>
            <div style="font-size: 0.8rem; color: #ef4444; text-transform: uppercase;">Critical SOS</div>
            <div id="hud-sos-count" class="metric-value" style="color: #ef4444;">0{{ $sosCount }}</div>
        </div>
        <div id="sos-icon-anim" style="color: #ef4444; font-size: 2rem;">🚨</div>
    </div>
    <div class="hud-panel metric-box">
        <div>
            <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Sector Weather</div>
            <div class="metric-value" style="font-size: 1.2rem; margin-top: 5px;">WIND: 15 KNT</div>
        </div>
        <div style="color: #f59e0b; font-size: 2rem;">⛈️</div>
    </div>
</div>

<div class="hud-panel right-hud p-3 d-flex flex-column">
    <div class="hud-header">📡 LIVE CONNECTION FEED</div>
    <div id="log-container" style="flex-grow: 1; overflow-y: hidden; margin-top: 10px;"></div>
    <div class="mt-auto pt-3 border-top border-secondary">
        <div class="d-flex justify-content-between align-items-center">
            <span style="font-size: 0.8rem; color: #94a3b8;">RADAR NETWORK</span>
            <span id="network-status" class="badge bg-success" style="font-size: 0.7rem; letter-spacing: 1px;">ACTIVE</span>
        </div>
    </div>
</div>

<div class="modal fade" id="systemMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-content modal-dialog modal-sm modal-dialog-centered modal-content-hud text-center">
        <div class="modal-body p-4">
            <div id="sys-msg-icon" style="font-size: 3rem; color: #38bdf8;">⛴️</div>
            <h5 class="text-white mt-3 fw-bold" style="letter-spacing: 1px;">SYSTEM NOTICE</h5>
            <p id="sys-msg-text" class="text-secondary small mt-2">Pesan akan muncul di sini.</p>
            <button type="button" class="btn btn-outline-info btn-sm mt-3 px-4 fw-bold" data-bs-dismiss="modal">ACKNOWLEDGE</button>
        </div>
    </div>
</div>

<div class="modal fade" id="directoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-content modal-dialog modal-xl modal-dialog-centered modal-content-hud">
        <div class="modal-header modal-header-hud">
            <h5 class="modal-title fw-bold">📋 REGISTERED DEVICE DIRECTORY</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-nowrap" style="color: #38bdf8; border-color: rgba(56, 189, 248, 0.3) !important;">
                        <span style="font-size: 1.2rem; margin-right: 8px;">⛴️</span> MILITARY PATROL UNITS
                    </h6>
                    <div style="max-height: 450px; overflow-y: auto; padding-right: 10px;">
                        <table class="table table-hud table-borderless table-patrol text-center w-100">
                            <thead style="position: sticky; top: 0; background: rgba(5,11,20,0.95); z-index: 1;">
                                <tr>
                                    <th class="text-start">KODE KRI</th>
                                    <th>STATUS</th>
                                    <th>KONDISI</th>
                                </tr>
                            </thead>
                            <tbody id="dir-patrol-list">
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-nowrap" style="color: #10b981; border-color: rgba(16, 185, 129, 0.3) !important;">
                        <span style="font-size: 1.2rem; margin-right: 8px;">⚓</span> CIVILIAN & ALIEN TARGETS
                    </h6>
                    <div style="max-height: 450px; overflow-y: auto; padding-right: 10px;">
                        <table class="table table-hud table-borderless table-civilian text-center w-100">
                            <thead style="position: sticky; top: 0; background: rgba(5,11,20,0.95); z-index: 1;">
                                <tr>
                                    <th class="text-start">SN / MAC</th>
                                    <th>STATUS</th>
                                    <th>KONDISI</th>
                                </tr>
                            </thead>
                            <tbody id="dir-device-list">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-content modal-dialog modal-content-hud">
        <div class="modal-header modal-header-hud">
            <h5 class="modal-title fw-bold">⚓ BIND NEW DEVICE</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="formRegistrasi">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-warning fw-bold">KODE PERANGKAT (SN)</label>
                    <input type="text" id="device_code" class="form-control form-control-hud border-warning" placeholder="Ex: MARGU-X001" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Base Latitude</label>
                        <input type="number" step="any" id="latitude" class="form-control form-control-hud" value="1.115" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-white">Base Longitude</label>
                        <input type="number" step="any" id="longitude" class="form-control form-control-hud" value="104.048" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white">Initial Status</label>
                    <select id="status" class="form-select form-control-hud" required>
                        <option value="OFFLINE" selected>⚫ OFFLINE (Belum Aktif)</option>
                        <option value="SECURE">🟢 SECURE (Langsung Melaut)</option>
                        <option value="PATROL">⛴️ PATROL (Kapal TNI AL)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary text-white" data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-info fw-bold text-white px-4">BIND DEVICE</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="analyticsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-content modal-dialog modal-content-hud">
        <div class="modal-header modal-header-hud">
            <h5 class="modal-title fw-bold">📊 SYSTEM ANALYTICS</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white">
            <div class="d-flex justify-content-between mb-2"><span>System Uptime:</span> <span class="text-success fw-bold">99.98%</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Database Ping:</span> <span class="text-info fw-bold">12 ms</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Active Satellites:</span> <span class="text-warning fw-bold">8 Satellites</span></div>
            <div class="d-flex justify-content-between mb-2"><span>Data Encryption:</span> <span class="text-secondary fw-bold">AES-256 Active</span></div>
            <hr class="border-secondary">
            <p class="text-info text-center mb-0" style="font-size:0.8rem; letter-spacing: 1px;">ALL SYSTEMS NOMINAL / OPTIMAL</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // =====================================================================
    // FUNGSI SIMULASI ALIEN SIGNAL (DENGAN PENAMBAHAN API TOKEN KEAMANAN)
    // =====================================================================
    function simulateUnknownDevice() {
        fetch('/api/hardware/ping', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ 
                api_token: 'MARGU-SECURE-KEY-2026', // <-- INI ADALAH PENAMBAHAN TOKEN KEAMANANNYA
                device_code: 'ALIEN-X99', 
                latitude: 1.150, 
                longitude: 104.120 
            })
        }).then(res => res.json()).then(data => {
            if(!data.success) {
                showSystemMessage(data.message, "🛑"); // Menampilkan pesan error jika token salah
            }
        });
    }

    // FUNGSI PENGHILANG LOADING SCREEN
    window.addEventListener('load', function() {
        setTimeout(function() {
            let loader = document.getElementById('startup-loader');
            loader.style.opacity = '0';
            setTimeout(() => { loader.style.visibility = 'hidden'; }, 800);
        }, 2200); 
    });

    function showSystemMessage(message, iconHtml = '⚠️') {
        document.getElementById('sys-msg-text').innerText = message;
        document.getElementById('sys-msg-icon').innerHTML = iconHtml;
        new bootstrap.Modal(document.getElementById('systemMessageModal')).show();
    }

    document.addEventListener("DOMContentLoaded", function() {
        var map = L.map('radarMap', { zoomControl: false }).setView([1.10, 104.05], 11);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '© Margu Tactical Systems', maxZoom: 19
        }).addTo(map);

        var vesselLayerGroup = L.layerGroup().addTo(map);
        var alarmSound = new Audio('https://actions.google.com/sounds/v1/alarms/spaceship_alarm.ogg');
        alarmSound.loop = true;
        var audioEnabled = false;
        let currentVessels = [];

        document.getElementById('audio-toggle').addEventListener('click', function() {
            audioEnabled = !audioEnabled;
            if (audioEnabled) {
                this.innerText = "🔊 ALARM SYSTEM: ARMED"; this.classList.add('active');
            } else {
                this.innerText = "🔇 ALARM SYSTEM: STANDBY"; this.classList.remove('active');
                alarmSound.pause(); alarmSound.currentTime = 0; 
            }
        });

        document.getElementById('btn-live').addEventListener('click', function() {
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active'); map.flyTo([1.10, 104.05], 11); 
        });

        document.getElementById('btn-emergency').addEventListener('click', function() {
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            let sosTargets = currentVessels.filter(v => v.status === 'SOS');
            if(sosTargets.length > 0) {
                if (sosTargets.length === 1) { map.flyTo([parseFloat(sosTargets[0].latitude), parseFloat(sosTargets[0].longitude)], 15); } 
                else {
                    let bounds = L.latLngBounds(sosTargets.map(v => [parseFloat(v.latitude), parseFloat(v.longitude)]));
                    map.flyToBounds(bounds, {padding: [50, 50]});
                }
            } else { showSystemMessage("Tidak ada sinyal darurat (SOS) yang aktif di perairan saat ini.", "🚨"); }
        });

        document.getElementById('btn-patrol').addEventListener('click', function() {
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            let patrolTarget = currentVessels.find(v => v.status === 'PATROL');
            if(patrolTarget) { map.flyTo([parseFloat(patrolTarget.latitude), parseFloat(patrolTarget.longitude)], 14); } 
            else { showSystemMessage("SYARAT DITOLAK: Tidak ada unit patroli TNI AL (KRI) yang sedang bertugas di radar.", "⛴️"); }
        });

        function fetchVesselsData() {
            fetch('/api/vessels')
                .then(response => response.json())
                .then(vessels => {
                    currentVessels = vessels; 
                    vesselLayerGroup.clearLayers();
                    
                    let sosCount = 0;
                    let activeSignalsCount = 0;
                    let unknownActive = false;
                    
                    let patrolListHTML = "";
                    let deviceListHTML = "";

                    vessels.forEach(function(vessel) {
                        if (vessel.status !== 'OFFLINE') {
                            let statusBadge = "";
                            
                            if(vessel.status === 'SECURE') statusBadge = '<span class="badge bg-success">SECURE</span>';
                            else if(vessel.status === 'SOS') { statusBadge = '<span class="badge bg-danger">SOS</span>'; }
                            else if(vessel.status === 'UNKNOWN') { statusBadge = '<span class="badge bg-warning text-dark">UNKNOWN</span>'; }
                            else if(vessel.status === 'PATROL') statusBadge = '<span class="badge bg-info">PATROL</span>';

                            let rowHTML = `
                                <tr>
                                    <td class="text-start fw-bold" style="color: #e2e8f0;">${vessel.device_code}</td>
                                    <td>${statusBadge}</td>
                                    <td style="color: #cbd5e1; font-size: 0.8rem;">🔋 ${vessel.battery_level}% | 📶 ${vessel.signal_strength}</td>
                                </tr>
                            `;

                            if (vessel.status === 'PATROL') { patrolListHTML += rowHTML; } 
                            else { deviceListHTML += rowHTML; }
                        }

                        if (vessel.status === 'OFFLINE') return;
                        let lat = parseFloat(vessel.latitude);
                        let lng = parseFloat(vessel.longitude);
                        if (isNaN(lat) || isNaN(lng) || lat === 0) return;
                        
                        activeSignalsCount++; 
                        let traceCoords = [];
                        try { traceCoords = JSON.parse(vessel.trace_history); } catch(e) {}
                        if(traceCoords && traceCoords.length > 1) {
                            L.polyline(traceCoords, { color: vessel.status === 'UNKNOWN' ? '#f59e0b' : '#38bdf8', weight: 2, opacity: 0.5, dashArray: '5, 5' }).addTo(vesselLayerGroup);
                        }

                        let healthStats = `
                            <div class="popup-data-row mt-2 pt-2" style="border-top: 1px dashed rgba(255,255,255,0.2);">
                                <span class="popup-label">Hardware Health:</span> <b class="text-white">🔋 ${vessel.battery_level}% &nbsp;|&nbsp; 📶 ${vessel.signal_strength}</b>
                            </div>
                        `;

                        let marker; 

                        if (vessel.status === 'UNKNOWN') {
                            unknownActive = true;
                            var unknownIcon = L.divIcon({ className: '', html: '<div class="sos-container"><div class="unknown-blinker"></div><div class="unknown-core"></div></div>', iconSize: [40, 40], iconAnchor: [20, 20] });
                            marker = L.marker([lat, lng], {icon: unknownIcon}).bindPopup(`
                                <div class="popup-header text-warning">⚠️ UNDECLARED DEVICE</div>
                                <div class="popup-data-row"><span class="popup-label">Target MAC/SN:</span> <b class="text-white">${vessel.device_code}</b></div>
                                <div class="popup-data-row"><span class="popup-label">Status:</span> <b class="text-warning">UNKNOWN / ILLEGAL</b></div>
                                ${healthStats}
                            `, { className: 'unknown-popup' }).addTo(vesselLayerGroup);
                        }
                        else if (vessel.status === 'SOS') {
                            sosCount++;
                            var sosIcon = L.divIcon({ className: '', html: '<div class="sos-container"><div class="sos-blinker"></div><div class="sos-core"></div></div>', iconSize: [40, 40], iconAnchor: [20, 20] });
                            marker = L.marker([lat, lng], {icon: sosIcon}).bindPopup(`
                                <div class="popup-header text-danger">🚨 CRITICAL SOS BROADCAST</div>
                                <div class="popup-data-row"><span class="popup-label">Device SN:</span> <b class="text-white">${vessel.device_code}</b></div>
                                <div class="popup-data-row"><span class="popup-label">Threat:</span> <b class="text-danger">SINKING / ACCIDENT</b></div>
                                ${healthStats}
                            `, { className: 'sos-popup' }).addTo(vesselLayerGroup);
                        }
                        else if (vessel.status === 'SECURE') {
                            var secureIcon = L.divIcon({ className: '', html: '<div class="secure-core"></div>', iconSize: [10, 10], iconAnchor: [5, 5] });
                            marker = L.marker([lat, lng], {icon: secureIcon}).bindPopup(`
                                <div class="popup-header text-info">⛴️ VESSEL ON TRACK</div>
                                <div class="popup-data-row"><span class="popup-label">Device SN:</span> <b class="text-white">${vessel.device_code}</b></div>
                                <div class="popup-data-row"><span class="popup-label">Status:</span> <b class="text-success fw-bold">SECURE</b></div>
                                ${healthStats}
                            `).addTo(vesselLayerGroup);
                        }
                        else if (vessel.status === 'PATROL') {
                            var patrolIcon = L.divIcon({ className: '', html: '<div style="font-size: 24px; text-shadow: 0 0 10px #38bdf8; transform: rotate(-45deg);">⛴️</div>', iconSize: [30, 30], iconAnchor: [15, 15] });
                            marker = L.marker([lat, lng], {icon: patrolIcon}).bindPopup(`
                                <div class="popup-header text-white">⚔️ MILITARY PATROL UNIT</div>
                                <div class="popup-data-row"><span class="popup-label">Device SN:</span> <b class="text-white">${vessel.device_code}</b></div>
                                <div class="popup-data-row"><span class="popup-label">Status:</span> <b class="text-info fw-bold">ON PATROL</b></div>
                                ${healthStats}
                            `).addTo(vesselLayerGroup);
                        }

                        if (marker) {
                            marker.on('mouseover', function(e) { this.openPopup(); });
                            marker.on('mouseout', function(e) { this.closePopup(); });
                        }
                    });

                    if(patrolListHTML === "") patrolListHTML = '<tr><td colspan="3" class="text-center text-secondary py-4">TIDAK ADA UNIT PATROLI AKTIF</td></tr>';
                    if(deviceListHTML === "") deviceListHTML = '<tr><td colspan="3" class="text-center text-secondary py-4">TIDAK ADA TARGET TERDETEKSI</td></tr>';
                    
                    document.getElementById('dir-patrol-list').innerHTML = patrolListHTML;
                    document.getElementById('dir-device-list').innerHTML = deviceListHTML;

                    document.getElementById('hud-total-signals').innerText = activeSignalsCount;
                    document.getElementById('hud-sos-count').innerText = sosCount < 10 ? '0' + sosCount : sosCount;
                    document.getElementById('hud-sos-badge').innerText = sosCount;

                    var sosIconAnim = document.getElementById('sos-icon-anim');
                    var sosPanelBox = document.getElementById('sos-panel-box');
                    
                    if (sosCount > 0) {
                        sosIconAnim.style.animation = "hud-blink 1s ease-in-out infinite";
                        sosPanelBox.style.backgroundColor = "rgba(239, 68, 68, 0.1)";
                        if (audioEnabled && alarmSound.paused) { alarmSound.play().catch(e => console.log("Audio fail")); }
                    } else {
                        sosIconAnim.style.animation = "none";
                        sosPanelBox.style.backgroundColor = "transparent";
                        alarmSound.pause(); alarmSound.currentTime = 0;
                    }

                    var now = new Date().toLocaleTimeString('id-ID');
                    var logContainer = document.getElementById('log-container');
                    var logHTML = `<div class="log-entry" style="border-left-color: #10b981;"><div class="log-time">${now} WIB</div><span class="text-success">[PING]</span> ${activeSignalsCount} targets online.</div>`;
                    if(unknownActive) { logHTML += `<div class="log-entry" style="border-left-color: #f59e0b; background: rgba(245, 158, 11, 0.1); padding: 5px;"><div class="log-time">${now} WIB</div><span class="text-warning fw-bold">[WARNING]</span> Unknown alien device tracking!</div>`; }
                    if(sosCount > 0) { logHTML += `<div class="log-entry" style="border-left-color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 5px;"><div class="log-time">${now} WIB</div><span class="text-danger fw-bold">[CRITICAL]</span> SOS Broadcast Active!</div>`; }
                    logContainer.innerHTML = logHTML;
                });
        }

        document.getElementById('formRegistrasi').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('/api/vessels/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    device_code: document.getElementById('device_code').value,
                    latitude: document.getElementById('latitude').value,
                    longitude: document.getElementById('longitude').value,
                    status: document.getElementById('status').value,
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                    modal.hide(); document.getElementById('formRegistrasi').reset(); fetchVesselsData();
                } else { alert("Gagal mendaftar. Pastikan Serial Number unik!"); }
            });
        });

        fetchVesselsData();
        setInterval(fetchVesselsData, 3000);
    });
</script>

</body>
</html>