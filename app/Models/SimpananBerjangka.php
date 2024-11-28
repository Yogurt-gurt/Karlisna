<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpananBerjangka extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'simpanan_berjangka';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_simpanan',
        'user_id',
        'bank',
        'nominal',
        'jangka_waktu_simpanan',
        'jumlah_jasa_perbulan',
        'status_manager',
        'status_ketua',
    ];

    /**
     * Get the user associated with this simpanan sukarela.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
