@extends('layouts.app')

@section('content')
    <h1>Daftar Pengajuan Pinjaman</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nominal Pinjaman</th>
                <th>Jangka Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengajuanPinjaman as $pengajuan)
                <tr>
                    <td>{{ $pengajuan->id }}</td>
                    <td>{{ $pengajuan->nominal_pinjaman }}</td>
                    <td>{{ $pengajuan->jangka_waktu }} bulan</td>
                    <td>{{ $pengajuan->status }}</td>
                    <td>
                        <a href="{{ route('pengajuan.show', $pengajuan->id) }}" class="btn btn-info">Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
