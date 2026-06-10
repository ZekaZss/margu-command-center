<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('device_code')->unique(); 
            $table->decimal('latitude', 10, 8)->nullable(); 
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('status')->default('OFFLINE'); 
            
            // FITUR 3: Signal Health Check
            $table->integer('battery_level')->default(100); 
            $table->string('signal_strength')->default('STRONG'); 
            
            // FITUR 2: Movement Traces (Menyimpan deretan kordinat dalam bentuk JSON)
            $table->json('trace_history')->nullable(); 
            
            // FITUR 1: Undeclared Devices (Menandai perangkat resmi vs perangkat asing)
            $table->boolean('is_registered')->default(true); 

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vessels');
    }
};