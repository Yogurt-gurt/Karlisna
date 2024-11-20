<!-- resources/views/admin/pengajuan/index.blade.php -->

@extends('layouts.admin')

@section('content')
    <h1>Daftar Pengajuan Pinjaman</h1>

    <table class="table table-striped">
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
                        <!-- Edit Button -->
                        <a href="{{ route('admin.pengajuan.edit', $pengajuan->id) }}" class="btn btn-warning">Edit</a>
                        
                        <!-- Approve Button -->
                        @if($pengajuan->status == 'Pending')
                            <form action="{{ route('admin.pengajuan.approve', $pengajuan->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">Terima</button>
                            </form>
                        @endif

                        <!-- Reject Button -->
                        @if($pengajuan->status == 'Pending')
                            <form action="{{ route('admin.pengajuan.reject', $pengajuan->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger">Tolak</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
