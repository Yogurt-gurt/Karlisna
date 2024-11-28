<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Pastikan namespace User sesuai dengan struktur proyek Anda

class Rekening extends Model
{
    use HasFactory;

    protected $table = 'rekenings'; // Nama tabel yang digunakan model ini

    /**
     * Kolom yang dapat diisi (mass assignable).
     */
    protected $fillable = [
        'nomor_rekening',
        'jenis_bank',
        'is_rekening_utama',
        'user_id', // Kolom foreign key untuk relasi ke tabel users
        'nama',    // Kolom untuk menyimpan nama pengguna (jika ada langsung di tabel rekenings)
    ];

    /**
     * Kolom yang akan dikonversi secara otomatis.
     */
    protected $casts = [
        'is_rekening_utama' => 'boolean', // Konversi nilai ke boolean
    ];

    /**
     * Relasi ke model User.
     * Menghubungkan rekening dengan pengguna (user_id).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Accessor untuk nama pengguna.
     * Prioritas: kolom 'nama' di tabel rekenings -> nama dari relasi User.
     */
    public function getNamaAttribute()
    {
        // Jika kolom 'nama' di tabel rekening ada, gunakan itu
        if (!empty($this->attributes['nama'])) {
            return $this->attributes['nama'];
        }

        // Jika tidak, fallback ke nama dari tabel users (relasi)
        return $this->user ? $this->user->name : 'Nama Tidak Tersedia';
    }

    /**
     * Relasi ke model PengajuanPinjaman.
     */
    public function pengajuanPinjaman()
    {
        return $this->hasMany(PengajuanPinjaman::class, 'rekening_id');
    }
}
