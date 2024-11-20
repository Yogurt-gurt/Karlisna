<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class PengajuanPinjaman extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dengan default (opsional jika tabelnya sesuai dengan penamaan model Laravel)
    protected $table = 'pinjaman_non_angunan';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'nomor_pinjaman',
        'nominal_pinjaman',
        'jangka_waktu',
        'nominal_angsuran',
        'status',
        'user_id',   // user_id bisa null
        'rekening_id', // rekening_id bisa null
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke model Rekening
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }
}
