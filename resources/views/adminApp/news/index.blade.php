@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Pengajuan Pinjaman</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Nominal Pinjaman</th>
                    <th>Jangka Waktu</th>
                    <th>Nominal Angsuran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengajuanPinjaman as $pengajuan)
                    <tr>
                        <td>{{ $pengajuan->nominal_pinjaman }}</td>
                        <td>{{ $pengajuan->jangka_waktu }} Bulan</td>
                        <td>{{ $pengajuan->nominal_angsuran }}</td>
                        <td>{{ $pengajuan->status }}</td>
                        <td>
                            <a href="{{ route('admin.pengajuan.edit', $pengajuan->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger">Tolak</button>
                            </form>
                            <form action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">Terima</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
