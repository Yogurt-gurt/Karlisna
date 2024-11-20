<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Rekening extends Model
{
    use HasFactory;

    protected $table = 'rekenings'; // Nama tabel jika berbeda dengan penamaan otomatis

    /**
     * Kolom yang dapat diisi (mass assignable).
     */
    protected $fillable = [
        'nomor_rekening',
        'jenis_bank',
        'is_rekening_utama',
        'user_id', // Kolom foreign key yang terhubung ke tabel users
        'nama', // Kolom untuk menyimpan nama pengguna dari tabel users
    ];

    /**
     * Relasi ke model User.
     * Menghubungkan rekening dengan pengguna.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor untuk mendapatkan nama pengguna dari relasi User.
     * Jika kolom 'nama' tidak ada dalam rekenings, ini akan mengambil nama dari users.
     */
    public function getNamaAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

     // Relasi ke model PengajuanPinjaman
    public function pengajuanPinjaman()
{
    return $this->hasMany(PengajuanPinjaman::class, 'rekening_id');
}
}
