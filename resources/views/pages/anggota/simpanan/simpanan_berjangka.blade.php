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

            <h2>Simpanan Berjangka</h2>
        </div>

        <!-- Form -->
        <form action="{{ route('simpanan-sukarela') }}" method="POST">
            @csrf

            <!-- Jumlah Transfer -->
            <div class="form-group">
                <label for="amount">Jumlah Transfer (IDR)</label>
                <input type="number" id="amount" name="amount" class="form-control" placeholder="Masukan Nominal"
                    min="10000" required>
            </div>

            <!-- Pilihan Bank Transfer -->
            <div class="form-group">
                <label for="payment_method">Pilih Bank Transfer</label>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="VIRTUAL_ACCOUNT_BRI">Bank BRI</option>
                    <option value="VIRTUAL_ACCOUNT_BCA">Bank BCA</option>
                    <option value="VIRTUAL_ACCOUNT_MANDIRI">Bank Mandiri</option>
                    <option value="VIRTUAL_ACCOUNT_BNI">Bank BNI</option>
                    <option value="VIRTUAL_ACCOUNT_BTN">Bank BTN</option>
                    <option value="VIRTUAL_ACCOUNT_DANAMON">Bank Danamon</option>
                    <option value="VIRTUAL_ACCOUNT_PERMATA">Bank Permata</option>
                    <option value="VIRTUAL_ACCOUNT_CIMB">Bank CIMB Niaga</option>
                    <option value="VIRTUAL_ACCOUNT_OCBC">Bank OCBC NISP</option>
                    <option value="VIRTUAL_ACCOUNT_UOB">Bank UOB</option>
                    <option value="VIRTUAL_ACCOUNT_BTPN">Bank BTPN</option>
                    <option value="VIRTUAL_ACCOUNT_MAYBANK">Bank Maybank</option>
                    <option value="VIRTUAL_ACCOUNT_MEGA">Bank Mega</option>
                    <option value="VIRTUAL_ACCOUNT_BJB">Bank BJB</option>
                    <option value="VIRTUAL_ACCOUNT_BNI_SYARIAH">BNI Syariah</option>
                    <option value="VIRTUAL_ACCOUNT_MANDIRI_SYARIAH">Mandiri Syariah</option>
                    <option value="VIRTUAL_ACCOUNT_MUAMALAT">Bank Muamalat</option>
                    <option value="VIRTUAL_ACCOUNT_BRI_SYARIAH">BRI Syariah</option>
                    <option value="VIRTUAL_ACCOUNT_BCA_SYARIAH">BCA Syariah</option>
                </select>
            </div>

            <!-- Tombol Kirim -->
            <button type="submit" class="btn btn-primary mt-4">LANJUTKAN</button>
        </form>

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
