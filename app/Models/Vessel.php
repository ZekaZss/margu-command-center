<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    use HasFactory;

    // Baris ini berfungsi untuk membuka kunci pengamanan Laravel, 
    // sehingga semua kolom di tabel vessels bisa kita isi dengan data.
    protected $guarded = [];
}