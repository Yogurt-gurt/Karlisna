<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <h2>
            MAIN MENU
        </h2>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('home-anggota') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#simpanan" aria-expanded="false" aria-controls="simpanan">
                <i class="fas fa-piggy-bank menu-icon"></i>
                <span class="menu-title">Simpanan</span>
            </a>
            <x-nav-link toggleId="simpanan" href="{{ route('simpanan-wajib') }}">Simpanan Wajib</x-nav-link>
            <x-nav-link toggleId="simpanan" href="{{ route('simpanan-pokok') }}">Simpanan Pokok</x-nav-link>
            <x-nav-link toggleId="simpanan" href="{{ route('simpanan-sukarela') }}">Simpanan Sukarela</x-nav-link>
            <x-nav-link toggleId="simpanan" href="{{ route('simpanan-berjangka') }}">Simpanan Berjangka</x-nav-link>

        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#pinjaman" aria-expanded="false" aria-controls="pinjaman">
                <i class="fas fa-wallet menu-icon"></i>
                <span class="menu-title">Pinjaman</span>
            </a>
            <x-nav-link toggleId="pinjaman" href="{{ route('pinjaman') }}">Data Pinjaman</x-nav-link>
            <x-nav-link toggleId="pinjaman" href="{{ route('pinjaman-regular') }}">Regular</x-nav-link>
            <x-nav-link toggleId="pinjaman" href="{{ route('pinjaman-emergency') }}">Emergency</x-nav-link>
            <x-nav-link toggleId="pinjaman" href="{{ route('pinjaman-angunan') }}">Angunan</x-nav-link>
            <x-nav-link toggleId="pinjaman" href="{{ route('pinjaman-nonangunan') }}">Tanpa Angunan</x-nav-link>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="pages/documentation/documentation.html">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">Laporan</span>
            </a>
            <div class="collapse" id="tables">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="pages/tables/basic-table.html">Basic table</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <i class="icon-head menu-icon"></i>
                <span class="menu-title">User Pages</span>
            </a>
        </li>
    </ul>
</nav>