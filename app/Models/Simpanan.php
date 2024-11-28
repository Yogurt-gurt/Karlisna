<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpananSukarela extends Model
{
    use HasFactory;

    protected $table = 'simpanan_sukarela';

    protected $fillable = [
        'invoice_number',
        'amount',
        'virtual_account_number',
        'status',
    ];
}
