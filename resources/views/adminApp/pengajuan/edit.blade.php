@extends('layouts.app')

@section('content')
    <h1>Edit Pengajuan Pinjaman</h1>

    <form action="{{ route('admin.pengajuan.update', $pengajuan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Nominal Pinjaman -->
        <div class="form-group">
            <label for="nominal_pinjaman">Nominal Pinjaman</label>
            <input type="number" class="form-control" id="nominal_pinjaman" name="nominal_pinjaman" 
                   value="{{ old('nominal_pinjaman', $pengajuan->nominal_pinjaman) }}" required>
        </div>

        <!-- Jangka Waktu -->
        <div class="form-group">
            <label for="jangka_waktu">Jangka Waktu</label>
            <input type="number" class="form-control" id="jangka_waktu" name="jangka_waktu" 
                   value="{{ old('jangka_waktu', $pengajuan->jangka_waktu) }}" required>
        </div>

        <!-- Nominal Angsuran (Hanya menampilkan nilai yang dihitung) -->
        <div class="form-group">
            <label for="nominal_angsuran">Nominal Angsuran</label>
            <input type="text" class="form-control" id="nominal_angsuran" name="nominal_angsuran" 
                   value="{{ old('nominal_angsuran', $pengajuan->nominal_angsuran) }}" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Update Pengajuan</button>
    </form>

    <script>
        // JavaScript untuk menghitung Nominal Angsuran secara otomatis
        document.getElementById('nominal_pinjaman').addEventListener('input', calculateAngsuran);
        document.getElementById('jangka_waktu').addEventListener('change', calculateAngsuran);

        function calculateAngsuran() {
            const nominalPinjaman = parseFloat(document.getElementById('nominal_pinjaman').value) || 0;
            const jangkaWaktu = parseInt(document.getElementById('jangka_waktu').value) || 0;
            const bungaPerTahun = 0.1;  // 10% bunga per tahun
            const bungaPerBulan = bungaPerTahun / 12;
            
            const totalBunga = nominalPinjaman * bungaPerBulan * jangkaWaktu;
            const totalPinjamanDenganBunga = nominalPinjaman + totalBunga;
            const nominalAngsuran = totalPinjamanDenganBunga / jangkaWaktu;
            
            document.getElementById('nominal_angsuran').value = nominalAngsuran.toFixed(2);
        }
    </script>
@endsection
