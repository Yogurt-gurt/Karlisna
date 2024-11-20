@if (auth()->user()->hasRole('manager'))
@extends('layouts.dashboard-layout')
@section('title', $title)
@section('content')
<div class="content-background">
    <div class="search-bar">
        <input type="text" placeholder="Search" class="form-control mr-2" style="width: 200px;" />
        <div class="ml-auto d-flex">
            <button type="submit" class="btn btn-success" onclick="updateStatus('Diterima')">Terima</button>
            <button type="submit" class="btn btn-danger" onclick="updateStatus('Ditolak')">Tolak</button>
        </div>
        @csrf


    </div>
    <div class="filter-buttons d-flex mt-3">
        <button onclick="filterData('all')" class="btn-link">All</button>
        <button onclick="filterData('diterima')" class="btn-link">Diterima</button>
        <button onclick="filterData('pengajuan')" class="btn-link">Belum Diterima</button>
        <button onclick="filterData('ditolak')" class="btn-link">Ditolak</button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="select-all">
                            <label class="custom-control-label" for="select-all"></label>
                        </div>
                    </th>
                    <th>Nama</th>
                    <th>Tempat Lahir</th>
                    <th>Tgl Lahir</th>
                    <th>NIK</th>
                    <th>Email Kantor</th>
                    <th>No Handphone</th>
                    <th>Alamat Domisili</th>
                    <th>Alamat KTP</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($anggota as $data)
                <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checkbox-item"
                                id="checkbox{{ $loop->index }}" data-id="{{ $data->id }}">
                            <label class="custom-control-label" for="checkbox{{ $loop->index }}"></label>
                        </div>
                    </td>
                    <td>{{ $data->nama }}</td>
                    <td>{{ $data->tempat_lahir }}</td>
                    <td>{{ $data->tgl_lahir }}</td>
                    <td>{{ $data->nik }}</td>
                    <td>{{ $data->email_kantor }}</td>
                    <td>{{ $data->no_handphone }}</td>
                    <td>{{ $data->alamat_domisili }}</td>
                    <td>{{ $data->alamat_ktp }}</td>
                    <td>
                        @if ($data->status_ketua == 'Diterima')
                        <span class="badge badge-border-success">diterima ketua</span>
                        @elseif($data->status_ketua == 'Ditolak')
                        <span class="badge badge-border-danger">ditolak ketua</span>
                        @elseif($data->status_manager == 'Diterima')
                        <span class="badge badge-border-warning">menunggu approve ketua</span>
                        @elseif($data->status_manager == 'Ditolak')
                        <span class="badge badge-border-danger">ditolak manager</span>
                        @else
                        <span class="badge badge-border-warning">Pengajuan</span>
                        @endif
                    </td>
                    <td class="action-icons">
                        <a href="#" class="action-icons" data-toggle="modal" data-target="#detailModal{{$data->id}}"><i class="fas fa-eye"></i></a>
                        <i class="fas fa-edit edit"></i>
                        <i class="fas fa-trash delete"></i>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Showing data details -->
    <div class="showing-info">
        Showing <span id="start"></span> to <span id="end"></span> of <span id="total"></span> entries
    </div>
</div>

{{-- <script>
            function Diterima() {
                Swal.fire({
                    title: "Approve Anggota?",
                    text: "Apakah anda yakin ingin menyetujui anggota ini?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const selectedCheckboxes = document.querySelectorAll('.checkbox-item:checked');
                        selectedCheckboxes.forEach(checkbox => {
                            const id = checkbox.getAttribute('data-id');
                            $.ajax({
                                url: "{{ route('approve.diterima-manager', ['id' => 'ID', 'status' => 'Diterima']) }}"
.replace('ID', id),
type: "POST",
data: {
_token: "{{ csrf_token() }}",
status: "Diterima"
},
success: function(response) {
Swal.fire({
title: "Approved!",
text: "Anggota telah disetujui.",
icon: "success"
}).then(() => {
location.reload();
});
},
error: function(xhr) {
Swal.fire("Error!", "Terjadi kesalahan");
}
});
});
}
});
}

function Ditolak() {
Swal.fire({
title: "Approve Anggota?",
text: "Apakah anda yakin ingin menolakx anggota ini?",
icon: "question",
showCancelButton: true,
confirmButtonColor: "#3085d6",
cancelButtonColor: "#d33",
confirmButtonText: "Yes!"
}).then((result) => {
if (result.isConfirmed) {
const selectedCheckboxes = document.querySelectorAll('.checkbox-item:checked');
selectedCheckboxes.forEach(checkbox => {
const id = checkbox.getAttribute('data-id');
$.ajax({
url: "{{ route('approve.ditolak-manager', ['id' => 'ID', 'status' => 'Ditolak']) }}"
.replace('ID', id),
type: "POST",
data: {
_token: "{{ csrf_token() }}",
status: "Ditolak"
},
success: function(response) {
Swal.fire({
title: "No Approved!",
text: "Anggota telah ditolak.",
icon: "success"
}).then(() => {
location.reload();
});
},
error: function(xhr) {
Swal.fire("Error!", "Terjadi kesalahan: ", "error");
}
});
});
}
});
}
</script> --}}

<script>
    function updateStatus(status) {
        let title = "Approve Anggota?";
        let text = status === "Diterima" ? "Apakah anda yakin ingin menyetujui anggota ini?" :
            "Apakah anda yakin ingin menolak anggota ini?";
        let successTitle = status === "Diterima" ? "Approved!" : "No Approved!";
        let successText = status === "Diterima" ? "Anggota telah disetujui." : "Anggota telah ditolak.";

        Swal.fire({
            title: title,
            text: text,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes!"
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedCheckboxes = document.querySelectorAll('.checkbox-item:checked');
                selectedCheckboxes.forEach(checkbox => {
                    const id = checkbox.getAttribute('data-id');
                    $.ajax({
                        url: "{{ route('approve.update-status-manager', ['id' => 'ID', 'status' => 'STATUS']) }}"
                            .replace('ID', id)
                            .replace('STATUS', status),
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: status
                        },
                        success: function(response) {
                            Swal.fire({
                                title: successTitle,
                                text: successText,
                                icon: "success"
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire("Error!", "Terjadi kesalahan: ", "error");
                        }
                    });
                });
            }
        });
    }
</script>

<Script>
    document.getElementById('select-all').addEventListener('change', function() {
        var isChecked = this.checked;
        var checkboxes = document.querySelectorAll('.checkbox-item');

        checkboxes.forEach(function(checkbox) {
            checkbox.checked = isChecked;
        });
    });

    // Event listener untuk mengambil ID dari checkbox yang dipilih
    document.querySelectorAll('.checkbox-item').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var id = this.dataset.id;
            if (this.checked) {
                console.log('Checkbox with ID ' + id + ' is checked.');
            } else {
                console.log('Checkbox with ID ' + id + ' is unchecked.');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Fetch count for All
        $.ajax({
            url: '/count-data/all',
            type: 'GET',
            success: function(response) {
                $('.btn-wrapper .count').eq(0).text(response.count);
            }
        });

        // Fetch count for Terima
        $.ajax({
            url: '/count-data/diterima',
            type: 'GET',
            success: function(response) {
                $('.btn-wrapper .count').eq(1).text(response.count);
            }
        });

        // Fetch count for Pengajuan (Belum Diterima)
        $.ajax({
            url: '/count-data/pengajuan',
            type: 'GET',
            success: function(response) {
                $('.btn-wrapper .count').eq(2).text(response
                    .count); // Update angka di tombol "Belum Diterima"
            }
        });


        // Fetch count for Ditolak
        $.ajax({
            url: '/count-data/ditolak',
            type: 'GET',
            success: function(response) {
                $('.btn-wrapper .count').eq(3).text(response.count);
            }
        });
    });

    function filterData(status) {
        // Lakukan filter berdasarkan status
        $.ajax({
            url: `/data/filter/${status}`,
            type: 'GET',
            success: function(response) {
                // Update tabel dengan data yang difilter
                $('tbody').html(response); // Sesuaikan dengan respon yang diberikan dari server
            }
        });
    }
</script>
<script>
    // Misalkan ada 24 data total dan hanya menampilkan 8 per halaman
    const totalData = 24;
    const perPage = 8;
    let currentPage = 1; // Anda bisa mengubah nilai ini sesuai nomor halaman

    function updateShowingInfo() {
        const start = (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, totalData);
        document.getElementById('start').innerText = start;
        document.getElementById('end').innerText = end;
        document.getElementById('total').innerText = totalData;
    }

    // Panggil fungsi saat halaman dimuat atau saat pagination diubah
    updateShowingInfo();
</script>
@endsection
@endif