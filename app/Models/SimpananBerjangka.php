<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SimpananBerjangka extends Model
{
    use HasFactory;

    protected $table = 'simpanan_berjangka'; // Tentukan nama tabel secara eksplisit

    protected $fillable = [
        'invoice_number',
        'amount',
        'jangka_waktu',
        'jumlah_jasa',
        'bank',
        'virtual_account_number',
        'status',
        'expired_at', // Tambahkan ini
    ];

    protected static function booted()



    {
        static::updating(function ($model) {
            if ($model->isDirty('status') && $model->status === 'expired') {
                Log::info("Status updated to expired for invoice: {$model->invoice_number}");
            }
        });
    }

}
