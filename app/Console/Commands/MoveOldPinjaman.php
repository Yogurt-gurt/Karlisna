<?php

namespace App\Console\Commands;

use App\Models\PinjamanEmergency;
use App\Models\PinjamanNonAngunan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MoveOldPinjaman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pinjaman:move-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pindahkan data dari pinjaman_emergencies ke pinjaman_non_angunan setelah 3 bulan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memindahkan data pinjaman emergency yang berumur lebih dari 3 bulan...');

        // Ambil data dari tabel pinjaman_emergencies yang sudah lebih dari 3 bulan
        $oldPinjaman = PinjamanEmergency::where('created_at', '<=', now()->subMonths(3))->get();

        if ($oldPinjaman->isEmpty()) {
            $this->info('Tidak ada data untuk dipindahkan.');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($oldPinjaman as $pinjaman) {
                // Pindahkan data ke tabel pinjaman_non_angunan
                PinjamanNonAngunan::create([
                    'nomor_pinjaman' => $pinjaman->nomor_pinjaman,
                    'nominal_pinjaman' => $pinjaman->nominal_pinjaman,
                    'jangka_waktu' => $pinjaman->jangka_waktu,
                    'nominal_angsuran' => $pinjaman->nominal_angsuran,
                    'status' => $pinjaman->status,
                    'user_id' => $pinjaman->user_id,
                    'rekening_id' => $pinjaman->rekening_id,
                    'keterangan' => $pinjaman->keterangan,
                ]);

                // Hapus data dari tabel pinjaman_emergencies
                $pinjaman->delete();
            }

            DB::commit();
            $this->info('Data berhasil dipindahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memindahkan data pinjaman: ' . $e->getMessage());
            $this->error('Terjadi kesalahan saat memindahkan data.');
        }
    }
}
