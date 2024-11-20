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

            <h2>Simpanan Sukarela</h2>
        </div>

        <!-- Form -->
        <form action="{{ route('payment-sukarela') }}" method="POST">
            @csrf

            <!-- Jumlah Transfer -->
            <div class="form-group">
                <label for="amount">Jumlah Transfer (IDR)</label>
                <input type="text" id="amount" name="amount" class="form-control" placeholder="Masukan Nominal"
                    required>
            </div>
            <!-- Pilihan Bank Transfer -->
            <div class="form-group">
                <label for="payment_method">Pilih Bank Transfer</label>
                <select id="path" name="path" class="form-control" required>
                    <option value="/bri-virtual-account/v2/payment-code">BANK BRI</option>
                    <option value="/bni-virtual-account/v2/payment-code">BANK BNI</option>
                    <option value="/bca-virtual-account/v2/payment-code">BANK BCA</option>
                    <option value="/mandiri-virtual-account/v2/payment-code">BANK MANDIRI</option>
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

        <script>
            document.getElementById('amount').addEventListener('input', function(e) {
                // Ambil nilai input
                let value = e.target.value;

                // Hapus karakter selain angka
                value = value.replace(/[^0-9]/g, '');

                // Format angka dengan pemisah ribuan
                let formattedValue = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                // Tambahkan prefix "Rp."
                e.target.value = 'Rp. ' + formattedValue;
            });
        </script>
        <script>
            document.getElementById('form-id').addEventListener('submit', function(e) {
                let amountField = document.getElementById('amount');
                let rawAmount = amountField.value.replace(/[^0-9]/g, ''); // Hapus "Rp." dan titik

                // Set nilai input menjadi angka murni
                amountField.value = rawAmount;
            });
        </script>
    @endsection
