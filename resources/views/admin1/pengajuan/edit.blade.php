@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Pengajuan Pinjaman - Admin 1</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin1.pengajuan.update', $pengajuan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="nominal_pinjaman" class="form-label">Nominal Pinjaman</label>
            <input type="number" class="form-control" id="nominal_pinjaman" name="nominal_pinjaman" value="{{ $pengajuan->nominal_pinjaman }}" required>
        </div>
        
        <div class="mb-3">
            <label for="jangka_waktu" class="form-label">Jangka Waktu (bulan)</label>
            <input type="number" class="form-control" id="jangka_waktu" name="jangka_waktu" value="{{ $pengajuan->jangka_waktu }}" required>
        </div>

        <div class="mb-3">
            <label for="nominal_angsuran" class="form-label">Nominal Angsuran</label>
            <input type="number" class="form-control" id="nominal_angsuran" name="nominal_angsuran" value="{{ $pengajuan->nominal_angsuran }}" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<script>
    document.getElementById('nominal_pinjaman').addEventListener('input', calculateAngsuran);
    document.getElementById('jangka_waktu').addEventListener('input', calculateAngsuran);

    function calculateAngsuran() {
        let nominalPinjaman = parseFloat(document.getElementById('nominal_pinjaman').value) || 0;
        let jangkaWaktu = parseInt(document.getElementById('jangka_waktu').value) || 1;
        let bungaPerTahun = 0.1;

        let bungaPerBulan = bungaPerTahun / 12;
        let totalBunga = nominalPinjaman * bungaPerBulan * jangkaWaktu;
        let totalPinjamanDenganBunga = nominalPinjaman + totalBunga;
        let angsuranPerBulan = totalPinjamanDenganBunga / jangkaWaktu;

        document.getElementById('nominal_angsuran').value = angsuranPerBulan.toFixed(0);
    }
</script>
@endsection
