<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;
    protected $table = 'anggota';

    protected $fillable = [
        'nama',
        'alamat_domisili',
        'tempat_lahir',
        'tgl_lahir',
        'alamat_ktp',
        'nik',
        'email_kantor',
        'no_handphone',
        'password',
        'status_manager',
        'status_ketua',
        'status',
    ];
}
