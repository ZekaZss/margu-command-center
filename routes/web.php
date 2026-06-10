<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Vessel;

Route::get('/', function () {
    $vessels = Vessel::all();
    $totalSignals = $vessels->where('status', '!=', 'OFFLINE')->count();
    $sosCount = $vessels->where('status', 'SOS')->count();
    return view('welcome', compact('totalSignals', 'sosCount'));
});

Route::get('/api/vessels', function () {
    return response()->json(Vessel::all());
});

// API REGISTRASI RESMI (Oleh TNI AL)
Route::post('/api/vessels/register', function (Request $request) {
    $request->validate([
        'device_code' => 'required|string|unique:vessels,device_code',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'status' => 'required|string',
    ]);

    // Format JSON array untuk titik awal
    $initialTrace = json_encode([[$request->latitude, $request->longitude]]);

    Vessel::create([
        'device_code' => $request->device_code,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'status' => $request->status,
        'battery_level' => 100, // Default penuh
        'signal_strength' => 'STRONG',
        'trace_history' => $initialTrace,
        'is_registered' => true
    ]);

    return response()->json(['success' => true]);
});

// API SIMULASI HARDWARE IoT PING (Untuk jam tangan mengirim koordinat berkala)
Route::post('/api/hardware/ping', function (Request $request) {
    $vessel = Vessel::where('device_code', $request->device_code)->first();
    $lat = $request->latitude;
    $lng = $request->longitude;

    if ($vessel) {
        // Jika alat resmi, tambahkan rekam jejak
        $traces = json_decode($vessel->trace_history, true) ?? [];
        $traces[] = [$lat, $lng];
        if(count($traces) > 30) array_shift($traces); // Batasi jejak maksimal 30 titik agar ringan

        $vessel->update([
            'latitude' => $lat,
            'longitude' => $lng,
            'battery_level' => rand(15, 95), // Simulasi baterai berkurang
            'signal_strength' => 'WEAK', // Simulasi cuaca buruk
            'trace_history' => json_encode($traces)
        ]);
    } else {
        // FITUR 1: ALAT ASING (UNDECLARED DEVICE)
        // Jika nomor seri tidak ada di DB, ciptakan sebagai ancaman UNKNOWN
        Vessel::create([
            'device_code' => $request->device_code,
            'latitude' => $lat,
            'longitude' => $lng,
            'status' => 'UNKNOWN',
            'battery_level' => 40,
            'signal_strength' => 'POOR',
            'trace_history' => json_encode([[$lat, $lng]]),
            'is_registered' => false
        ]);
    }
    return response()->json(['success' => true]);
});

Route::get('/simulator', function () {
    return view('simulator', ['vessels' => Vessel::all()]);
});