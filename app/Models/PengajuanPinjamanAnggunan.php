<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class PengajuanPinjamanAnggunan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_pinjamen_anggunan';

    protected $fillable = [
        'nominal_pinjaman',
        'jangka_waktu',
        'nominal_angsuran',
        'status',
        'user_id',
        'rekening_id',
        'jenis_anggunan',
        'file_anggunan',
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Rekening
     */
    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    
}
