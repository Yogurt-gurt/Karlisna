@extends('layouts.app')

@section('content')
    <h1>Pengajuan Pinjaman Baru</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pengajuan.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nominal_pinjaman">Nominal Pinjaman</label>
            <input type="number" name="nominal_pinjaman" class="form-control" id="nominal_pinjaman" required>
        </div>

        <div class="form-group">
            <label for="jangka_waktu">Jangka Waktu (bulan)</label>
            <select name="jangka_waktu" class="form-control" id="jangka_waktu" required>
                @foreach($jangkaWaktuOptions as $option)
                    <option value="{{ $option }}">{{ $option }} bulan</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="tujuan_pinjaman">Tujuan Pinjaman</label>
            <textarea name="tujuan_pinjaman" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="rekening_id">Pilih Nomor Rekening</label>
            <select name="rekening_id" class="form-control" required>
                @foreach($rekenings as $rekening)
                    <option value="{{ $rekening->id }}">{{ $rekening->nomor_rekening }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="nominal_angsuran">Nominal Angsuran</label>
            <input type="number" name="nominal_angsuran" class="form-control" id="nominal_angsuran" required readonly>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="checklist_1" required>
            <label class="form-check-label" >Pembayaran akan otomatis dipotong dari gaji Anda.</label>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="checklist_2" required>
            <label class="form-check-label" >Saya telah membaca dan memahami syarat pinjaman.</label>
        </div>

        <button type="submit" class="btn btn-primary">Ajukan Pinjaman</button>
    </form>

    <script>
        document.getElementById('nominal_pinjaman').addEventListener('input', function() {
            const nominalPinjaman = parseFloat(this.value);
            const jangkaWaktu = parseFloat(document.getElementById('jangka_waktu').value);
            
            if (!isNaN(nominalPinjaman) && !isNaN(jangkaWaktu)) {
                const bungaPerTahun = 0.1;
                const bungaPerBulan = bungaPerTahun / 12;
                const totalBunga = nominalPinjaman * bungaPerBulan * jangkaWaktu;
                const totalPinjamanDenganBunga = nominalPinjaman + totalBunga;
                const nominalAngsuran = totalPinjamanDenganBunga / jangkaWaktu;

                document.getElementById('nominal_angsuran').value = nominalAngsuran.toFixed(2); // Set nominal angsuran otomatis
            }
        });

        document.getElementById('jangka_waktu').addEventListener('change', function() {
            document.getElementById('nominal_pinjaman').dispatchEvent(new Event('input')); // Trigger perhitungan ulang
        });
    </script>
@endsection
