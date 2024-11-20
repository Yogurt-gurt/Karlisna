<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SimpananSukarela;
use Illuminate\Support\Str;

class SimpananSukarelaController extends Controller
{
    public function simpanansukarela(Request $request)
    {
        // Validasi input
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|string',
        ]);

        // Generate invoice number
        $invoiceNumber = 'INV-' . strtoupper(Str::random(10)); // Invoice unik

        // Simpan ke database
        $simpanan = SimpananSukarela::create([
            'invoice_number' => $invoiceNumber,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        // Simulasi nomor virtual account
        $virtualAccountNumber = 'VA-' . rand(100000, 999999); // VA simulasi
        $simpanan->update([
            'virtual_account_number' => $virtualAccountNumber,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', "Simpanan berhasil dibuat dengan Invoice: {$invoiceNumber} dan Nomor Virtual Account: {$virtualAccountNumber}");
    }
}
