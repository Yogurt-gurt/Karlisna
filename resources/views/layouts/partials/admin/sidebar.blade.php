<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <h2>
            MAIN MENU
        </h2>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('home-admin') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" role="button" aria-expanded="false" aria-controls="ui-basic">
                <i class="bi bi-person-square menu-icon"></i>
                <span class="menu-title">Anggota</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('data-anggota')}}">Data Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('laporan-regis')}}">Laporan Registrasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('mutasi-simpanan')}}">Mutasi Simpanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/ui-features/typography.html">Mutasi Pinjaman</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#charts" aria-expanded="false" aria-controls="charts">
                <i class="fas fa-piggy-bank menu-icon"></i>
                <span class="menu-title">Simpanan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="charts">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="pages/charts/chartjs.html">ChartJs</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
                <i class="fas fa-wallet menu-icon"></i>
                <span class="menu-title">Pinjaman</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="form-elements">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{route('laporan-pinjaman')}}">Laporan Pinjaman</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#news-elements" aria-expanded="false" aria-controls="news-elements">
                <i class="fas fa-newspaper menu-icon"></i>
                <span class="menu-title">Berita</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="news-elements">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link" href="{{route('kelola-news')}}">Laporan News</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#laporan-elements" aria-expanded="false" aria-controls="laporan-elements">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">Laporan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="laporan-elements">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="pages/tables/basic-table.html">Basic table</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <i class="icon-head menu-icon"></i>
                <span class="menu-title">User Pages</span>
                <i class="menu-arrow"></i>
            </a>
        </li>
    </ul>
    </li>

    <!-- Tambahkan elemen lain seperti di atas -->

    </ul>
</nav>