<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanEmergency extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pinjaman_emergencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomor_pinjaman',
        'nominal_pinjaman',
        'jangka_waktu',
        'nominal_angsuran',
        'status',
        'user_id',
        'rekening_id',
        'status_manager',
        'status_ketua',
        'status_bendahara',
        'checkbox_syarat_3',
        'checkbox_syarat_4',
        'checkbox_syarat_5',
        'keterangan',
    ];

    /**
     * Get the user that owns the loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account (rekening) associated with the loan.
     */
    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }
}
