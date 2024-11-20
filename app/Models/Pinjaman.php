<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;
use App\Models\Anggota;
use App\Models\PinjamanCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// class Pinjaman extends Model
// {
//     use HasFactory;
//     public const STATUS_OPTIONS = ['3 Bulan','6 Bulan','9 Bulan','12 Bulan','15 bulan','18 bulan','24 bulan'];
//     public const ANGUNAN_OPTIONS = ['SERTIFIKAT TANAH','SERTIFIKAT RUMAH','BPKB KENDARAAN','SURAT BERHARGA LAINNYA'];

//     protected $table = 'pinjamen';
//     protected $guarded = ['id'];
//     protected $with = ['categoryloan','user'];
//     public function categoryloan(): BelongsTo
//     {
//         return $this->belongsTo(PinjamanCategory::class, 'pinjaman_category_id');
//     }

//     public function user(): BelongsTo
//     {
//         return $this->belongsTo(User::class);
//     }

//     public function getRouteKeyName()
//     {
//         return 'uuid';
//     }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pinjaman extends Model
{
    use HasFactory;

    // Opsi-opsi yang digunakan untuk tenor dan angunan
    public const STATUS_OPTIONS = ['3 Bulan', '6 Bulan', '9 Bulan', '12 Bulan', '15 bulan', '18 bulan', '24 bulan'];
    public const ANGUNAN_OPTIONS = ['SERTIFIKAT TANAH', 'SERTIFIKAT RUMAH', 'BPKB KENDARAAN', 'SURAT BERHARGA LAINNYA'];

    // Tentukan tabel yang digunakan
    protected $table = 'pinjaman_non_angunan'; // Tabel yang sesuai dengan database Anda

    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'user_id',
        'rekening_id',
        'nomor_pinjaman',
        'nominal_pinjaman',
        'jangka_waktu',
        'nominal_angsuran',
        'status',
        'status_manager',
        'status_ketua',
        'status_bendahara',
        'keterangan'
    ];

    // Relasi dengan tabel kategori pinjaman
    public function categoryloan(): BelongsTo
    {
        return $this->belongsTo(PinjamanCategory::class, 'pinjaman_category_id');
    }



    // Mengubah kunci rute untuk penggunaan UUID
    public function getRouteKeyName()
    {
        return 'uuid';
    }
    // app/Models/User.php
    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }

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
