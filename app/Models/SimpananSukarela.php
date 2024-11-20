<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpananSukarela extends Model
{

    protected $table = 'simpanan_sukarela';

    use HasFactory;
    protected $fillable = [
        'invoice_number',
        'amount',
        'virtual_account_number',
        'status',
    ];
}
