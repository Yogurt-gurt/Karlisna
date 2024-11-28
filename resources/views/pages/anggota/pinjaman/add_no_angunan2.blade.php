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

        <h2>Pinjaman Non Angunan</h2>
    </div>

    <!-- Form -->
    <form action="{{ route('pinjaman.storeNonAngunan') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="nomor_pinjaman">Nomor Pinjaman</label>
            <input type="text" id="nomor_pinjaman" name="nomor_pinjaman" class="form-control" required placeholder="Masukkan Nomor Pinjaman" value="{{ old('nomor_pinjaman') }}">
        </div>

        <div class="form-group">
            <label for="nominal">Nominal Pinjaman (IDR)</label>
            <input type="number" id="nominal" name="nominal" class="form-control" required placeholder="Masukan Nominal" min="10000" value="{{ old('nominal') }}">
        </div>

        <div class="form-group">
            <label for="tenor">Jangka Waktu Peminjaman</label>
            <select id="tenor" name="tenor" class="form-control" required>
                <option value="">Pilih tenor</option>
                @foreach ($tenor as $item)
                <!-- Pastikan value yang dikirim adalah angka -->
                <option value="{{ (int)$item }}" {{ old('tenor') == (int)$item ? 'selected' : '' }}>
                    {{ ucfirst($item) }}
                </option>
                @endforeach
            </select>
        </div>


        <div class="form-group">
            <label for="keterangan">Tujuan Pinjaman</label>
            <textarea id="keterangan" name="keterangan" class="form-control" required placeholder="Masukan Tujuan">{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-group">
            <label for="no_rekening">Nomor Rekening</label>
            <input type="number" id="no_rekening" name="no_rekening" class="form-control" required value="{{ $noRekening }}">
        </div>

        <div class="form-group">
            <label for="nominal_angsuran">Nominal Angsuran Perbulan</label>
            <input type="number" id="nominal_angsuran" name="nominal_angsuran" class="form-control" required placeholder="Masukan Nominal Angsuran" value="{{ old('nominal_angsuran') }}">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary mt-4">LANJUTKAN</button>
        </div>
    </form>


</div>

@endsection