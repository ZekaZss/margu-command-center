<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Margu Simulator Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1e293b; color: #f8fafc; font-family: 'Segoe UI', sans-serif; padding: 50px; }
        .control-panel { background-color: #0f172a; padding: 30px; border-radius: 12px; border: 1px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        .btn-action { width: 100%; padding: 15px; font-weight: bold; font-size: 1.1rem; margin-bottom: 15px; border-radius: 8px; transition: 0.2s; }
        .btn-action:hover { transform: translateY(-3px); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 control-panel text-center">
            <h2 class="mb-1 text-info">⚓ SIMULATOR MARGU</h2>
            <p class="text-muted mb-4">Remote Control Penguji Sistem Radar Real-Time</p>

            <a href="/simulator/hard-reset" class="btn btn-warning btn-action text-dark mb-5" onclick="return confirm('Ini akan menghapus semua sisa data lama dan mereset ke pengaturan pabrik. Lanjutkan?')">
                ⚠️ 1. KLIK INI DULU (FACTORY RESET DATABASE)
            </a>

            <a href="/simulator/trigger-sos" class="btn btn-danger btn-action">
                🚨 2. PICU SOS (Nelayan Batam)
            </a>

            <a href="/simulator/move-kri" class="btn btn-primary btn-action">
                ⛴️ 3. MAJUKAN KRI PATROLI
            </a>

            <hr class="border-secondary my-4">

            <a href="/simulator/reset" class="btn btn-outline-success btn-action">
                ✅ 4. KEMBALIKAN KE AMAN (Matikan SOS)
            </a>
        </div>
    </div>
</div>

</body>
</html>