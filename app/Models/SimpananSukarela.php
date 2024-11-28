<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpananSukarela extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'simpanan_sukarela';

    // Kolom yang dapat diisi (mass-assignable)
    protected $fillable = [
        'no_simpanan',      // Nomor simpanan
        'user_id',          // Relasi ke user
        'bank',             // Nama bank
        'nominal',          // Nominal simpanan
        'status_manager',   // Status dari manager
        'status_ketua',     // Status dari ketua
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
