<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Vessel;

// ====================================================================
// 1. JALUR UTAMA RADAR (Menampilkan UI/Tampilan Desktop)
// ====================================================================
Route::get('/', function () {
    $vessels = Vessel::all();
    $totalSignals = $vessels->where('status', '!=', 'OFFLINE')->count();
    $sosCount = $vessels->where('status', 'SOS')->count();
    
    return view('welcome', compact('totalSignals', 'sosCount'));
});

// ====================================================================
// 2. API PENARIK DATA RADAR (Dipanggil otomatis tiap 3 detik oleh UI)
// ====================================================================
Route::get('/api/vessels', function () {
    return response()->json(Vessel::all());
});

// ====================================================================
// 3. API REGISTRASI RESMI (Dari Menu "BIND DEVICE" oleh Operator TNI)
// ====================================================================
Route::post('/api/vessels/register', function (Request $request) {
    $request->validate([
        'device_code' => 'required|string|unique:vessels,device_code',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'status' => 'required|string',
    ]);

    // Format JSON array untuk titik awal garis jejak (trace history)
    $initialTrace = json_encode([[$request->latitude, $request->longitude]]);

    Vessel::create([
        'device_code' => $request->device_code,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'status' => $request->status,
        'battery_level' => 100, // Default baterai 100%
        'signal_strength' => 'STRONG',
        'trace_history' => $initialTrace,
        'is_registered' => true
    ]);

    return response()->json(['success' => true]);
});

// ====================================================================
// 4. API HARDWARE IoT PING (Endpoint khusus untuk Jam Tangan Fisik)
// ====================================================================
Route::post('/api/hardware/ping', function (Request $request) {
    // LAPISAN KEAMANAN: Verifikasi Token Rahasia Militer
    $secretKey = 'MARGU-SECURE-KEY-2026'; 
    
    // Cek apakah alat mengirimkan kunci (API Token) yang benar
    if ($request->api_token !== $secretKey) {
        return response()->json([
            'success' => false,
            'message' => 'AKSES DITOLAK: Sinyal Ilegal Diblokir oleh Sistem Keamanan Margu.'
        ], 401);
    }

    // JIKA KUNCI BENAR, PROSES DATANYA
    $vessel = Vessel::where('device_code', $request->device_code)->first();
    $lat = $request->latitude;
    $lng = $request->longitude;

    if ($vessel) {
        // Jika alat resmi dan sudah terdaftar, update posisi dan statusnya
        $traces = json_decode($vessel->trace_history, true) ?? [];
        $traces[] = [$lat, $lng];
        
        // Batasi memori rekam jejak maksimal 30 titik agar server tidak berat
        if(count($traces) > 30) {
            array_shift($traces); 
        }

        $vessel->update([
            'latitude' => $lat,
            'longitude' => $lng,
            // Jika jam fisik mengirim data baterai/sinyal asli, pakai datanya. Jika tidak, pakai data acak untuk simulasi.
            'battery_level' => $request->battery_level ?? rand(15, 95), 
            'signal_strength' => $request->signal_strength ?? 'STRONG',
            'status' => $request->status ?? $vessel->status, // Memungkinkan tombol SOS fisik di jam merubah status
            'trace_history' => json_encode($traces)
        ]);
    } else {
        // Jika alat memiliki Token benar, tapi Serial Number belum diregistrasi (Perangkat Asing / Alien)
        Vessel::create([
            'device_code' => $request->device_code,
            'latitude' => $lat,
            'longitude' => $lng,
            'status' => 'UNKNOWN',
            'battery_level' => $request->battery_level ?? 100,
            'signal_strength' => $request->signal_strength ?? 'POOR',
            'trace_history' => json_encode([[$lat, $lng]]),
            'is_registered' => false
        ]);
    }
    
    return response()->json(['success' => true, 'message' => 'Data diterima dengan aman.']);
});