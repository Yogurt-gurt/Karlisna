@if (auth()->user()->roles == 'manager')
@extends('layouts.dashboard-layout')
@section('title', $title)
@section('content')


<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Welcome Manager {{ Auth()->user()->role }}</h3>
                        <h6 class="font-weight-normal mb-0">Pada Dashboard Koperasi Konsumen Karlisna PLN
                            Yogyakarta</h6>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
                            <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                    id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="true">
                                    <i class="mdi mdi-calendar"></i> Today (10 Oct 2024)
                                </button>
                                <div class="dropdown-menu dropdown-menu-right"
                                    aria-labelledby="dropdownMenuDate2">
                                    <a class="dropdown-item" href="#">January - March</a>
                                    <a class="dropdown-item" href="#">March - June</a>
                                    <a class="dropdown-item" href="#">June - August</a>
                                    <a class="dropdown-item" href="#">August - November</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Card Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card anggota shadow-sm border-0">
                    <h3><i class="fas fa-users icon text-primary"></i> Anggota</h3>
                    <p>Approved</p>
                    <p class="value approved text-success">1000 Orang</p>
                    <p>Not Approved</p>
                    <p class="value not-approved text-danger">200 Orang</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card pinjaman shadow-sm border-0">
                    <h3><i class="fas fa-hand-holding-usd icon text-warning"></i> Pinjaman</h3>
                    <p>Tanpa Jaminan</p>
                    <p class="value no-guarantee text-info">Rp 78.960.864</p>
                    <p>Dengan Jaminan</p>
                    <p class="value with-guarantee text-primary">Rp 426.731.229</p>
                </div>
            </div>
        </div>

        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
@endsection
@endif