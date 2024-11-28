@extends('layouts.dashboard-layout')
@section('title', $title)
@section('content')
    <div class="content-background">
        <!-- Error Display -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Panel -->
        <!-- Header -->
        <div class="header d-flex align-items-center mb-4">
            <!-- Icon with data-roles attribute -->
            <i class="fas fa-arrow-left mr-3" style="cursor: pointer;" onclick="goBackToHome(this)"
                data-roles="{{ auth()->user()->roles }}"></i>

            <h2>Pinjaman Emergency</h2>
        </div>

        <!-- Form -->
        <form action="{{ route('pinjaman.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="hidden" id="jenis_pinjaman" name="jenis_pinjaman" class="form-control" required
                    value="Pinjaman Emergency" readonly>
            </div>
            <div class="form-group">
                <input type="text" id="nomor_pinjaman" name="nomor_pinjaman" class="form-control" required
                    value="{{ $new_nmr }}" readonly>
            </div>

            <div class="form-group">
                <label for="nominal">Nominal Pinjaman (IDR)</label>
                <input type="number" id="nominal" name="nominal" class="form-control" required
                    placeholder="Masukan Nominal" min="10000">
            </div>

            <div class="form-group">
                <label for="bank">Jangka Waktu Peminjaman</label>
                <select id="tenor" name="tenor" class="form-control" required>
                    <option value="">Pilih tenor</option>
                    @foreach ($tenor as $item)
                        <option value="{{ $item }}">{{ ucfirst($item) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="keterangan">Tujuan Pinjaman</label>
                <input type="textarea" id="keterangan" name="keterangan" class="form-control" required
                    placeholder="Masukan Tujuan">
            </div>

            <div class="form-group">
                <label for="no_rekening">Nomor Rekening</label>
                <input type="number" id="no_rekening" name="no_rekening" class="form-control" required value="{{ $noRekening }}">
            </div>

            <div class="form-group">
                <label for="nominal_angsuran">Nominal Angsuran Perbulan</label>
                <input type="number" id="nominal_angsuran" name="nominal_angsuran" class="form-control" required
                    placeholder="Masukan Nominal">
            </div>

            <div class="info-text mt-3">
                Simpanan akan dibayar melalui Virtual Account dan pembayaran maksimal 1 x 24 JAM setelah Anda Mengajukan
                Simpanan.
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary mt-4">LANJUTKAN</button>
            </div>
        </form>
    </div>

    <script>
        function goBackToHome(element) {
            // Get the user's roles from the data-roles attribute
            const roles = element.getAttribute('data-roles');
            let route = '';

            // Determine the route based on the user's roles
            if (roles === 'anggota') {
                route = "{{ route('home-anggota') }}";
            } else if (roles === 'admin') {
                route = "{{ route('home-admin') }}";
            }

            // Redirect to the determined route
            if (route) {
                window.location.href = route;
            }
        }
    </script>
@endsection
