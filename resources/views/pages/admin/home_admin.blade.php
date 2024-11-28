@if (auth()->user()->hasRole('admin'))
@extends('layouts.dashboard-layout')
@section('title', $title)
@section('content')
<!-- Content row -->
<div class="row">
    <!-- Kas Card -->
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Kas</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Saldo Bank</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 481.716.190</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Saldo Kas</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 3.646.121</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-lg text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simpanan Card -->
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Simpanan</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Masuk</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 7.505.000</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Keluar</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 9.383.229</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-piggy-bank fa-lg text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pinjaman Card -->
    <div class="col-xl-4 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pinjaman</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Jasa</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 78.960.864</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Pokok</div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 426.731.229</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-check-alt fa-lg text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pinjaman Baru Bulan Ini Card -->
    <div class="col-xl-12 col-md-6 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pinjaman Baru Bulan
                            Ini</div>
                        <div class="row">
                            <div class="col">
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Total</div>
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 452.250.000</div>
                            </div>
                            <div class="col">
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Reguler</div>
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 407.250.000</div>
                            </div>
                            <div class="col">
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Emergency</div>
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 35.000.000</div>
                            </div>
                            <div class="col">
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Lain-lain</div>
                                <div class="h6 mb-1 font-weight-bold text-gray-800">Rp 10.000.000</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-lg text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@endif