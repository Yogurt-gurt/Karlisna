@extends('layouts.dashboard-layout')
@section('title', $title)
@section('content')

<div class="content-background">
    <!-- Search Bar and Add Button -->
    <h3>Laporan Mutasi Pinjaman</h3>
    <div class="search-bar d-flex align-items-center mb-3">
        <input type="text" placeholder="Search" class="form-control mr-2" style="width: 200px;" />
        <div class="icons">
            <i class="fas fa-print">
            </i>
            <i class="fas fa-file-pdf">
            </i>
            <i class="fas fa-file-excel">
            </i>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <table>
                <thead>
                    <tr>
                        <th>Kode Bagian</th>
                        <th>Nama Anggota</th>
                        <th>No Pinjaman</th>
                        <th>Jenis</th>
                        <th>Pokok Pinjaman</th>
                        <th>Tenor</th>
                        <th>Tgl</th>
                        <th>Saldo Sebelum</th>
                        <th>Angs. Pokok</th>
                        <th>Bunga Angs</th>
                        <th>Total Potongan</th>
                        <th>Saldo Akhir</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ADA PENGADAAN</td>
                        <td>SIGIT PURWANTO 6993109K</td>
                        <td>PREG0120220 00462</td>
                        <td><span class="badge">P.Sp</span></td>
                        <td>303,000,000</td>
                        <td>17/120</td>
                        <td>25/07/2024</td>
                        <td>262,600,000</td>
                        <td>2,252,000</td>
                        <td>3,030,000</td>
                        <td>5,555,000</td>
                        <td>260,075,000</td>
                        <td><button class="btn custom-blue-button ml-2">Bayar</button></td>

                    </tr>
                    <!-- Repeat similar <tr> rows as needed -->
                </tbody>
            </table>
    </div>
</div>
<style>
    .icons i {
        font-size: 24px;
        /* Adjust the size as needed */
        color: #007bff;
        /* Set the color you prefer */
        margin-right: 12px;
        /* Optional, space between icons */
        transition: color 0.3s ease;
        /* Smooth transition effect for color */
    }

    .icons i:hover {
        color: #28a745;
        /* Change the color on hover */
    }

    h3 {
        margin-bottom: 20px;
        /* Adjust the space below the title */
        text-align: center;
        /* Center-align the title */
    }

    .search-bar {
        margin-top: 20px;
        /* Adjust the space above the search bar */
    }


    /* CSS for 'Laporan Mutasi Pinjaman' heading */
    .sidebar h2 {
        font-size: 16px;
        /* Reduced font size */
        color: #007bff;
        /* Bright color for better visibility */
    }

    .sidebar ul li a i {
        font-size: 22px;
        /* Larger size for icons */
        color: #007bff;
        /* Bright color for icons */
    }

    /* Styling for the badge class to be green */
    .badge {
        background-color: #28a745;
        /* Green color */
        color: #fff;
        padding: 4px 8px;
        /* Adjusted padding for better visibility */
        font-size: 14px;
        border-radius: 3px;
        font-weight: bold;
    }

    /* Styling for the 'Bayar' button */
    .status-button {
        background-color: #007bff;
        /* Blue color for the button */
        color: #fff;
        border: none;
        padding: 6px 12px;
        /* Adjusted padding for a more prominent button */
        font-size: 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    .status-button:hover {
        background-color: #0056b3;
        /* Darker blue on hover */
    }
</style>


@endsection