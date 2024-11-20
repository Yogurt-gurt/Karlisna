@if (auth()->user()->roles == 'ketua')
    @extends('layouts.dashboard-layout')
    @section('title', $title)
    @section('content')
        <div class="content-background">
            <div class="search-bar">
                <input type="text" placeholder="Search" class="form-control mr-2" style="width: 200px;" />
                <div class="ml-auto d-flex">
                    <button type="submit" class="btn btn-success" onclick="Diterima('')">Terima</button>
                    <button type="submit" class="btn btn-danger" onclick="Ditolak('')">Tolak</button>
                </div>
                @csrf
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                            @if ($data->status_manager == 'Diterima')
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
                                        @else
                                            <span class="badge badge-border-warning">Pengajuan</span>
                                        @endif
                                    </td>
                                    <td class="action-icons">
                                        <i class="fas fa-edit edit"></i>
                                        <i class="fas fa-trash delete"></i>
                                    </td>
                                </tr>
                            @endif
                            <div class="card-container d-flex">
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            function Diterima() {
                const selectedCheckboxes = document.querySelectorAll('.checkbox-item:checked');

                if (selectedCheckboxes.length === 0) {
                    Swal.fire("Warning!", "Tidak ada anggota yang dipilih.", "warning");
                    return;
                }

                Swal.fire({
                    title: "Approve Anggota?",
                    text: "Apakah anda yakin ingin menyetujui anggota yang dipilih?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        selectedCheckboxes.forEach(checkbox => {
                            const id = checkbox.getAttribute('data-id');

                            $.ajax({
                                url: "{{ route('approve.diterima-ketua', ['id' => ':id', 'status' => 'Diterima']) }}"
                                    .replace(':id', id),
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    status: "Diterima"
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: "Approved!",
                                        text: "Anggota Berhasil diterima.",
                                        icon: "success"
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire("Error!", "Terjadi kesalahan: " + xhr.responseText,
                                        "error");
                                }
                            });
                        });
                    }
                });
            }

            function Ditolak() {
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
                                url: "{{ route('approve.ditolak-ketua', ['id' => 'ID', 'status' => 'Ditolak']) }}"
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

            document.getElementById('select-all').addEventListener('change', function() {
                var isChecked = this.checked;
                var checkboxes = document.querySelectorAll('.checkbox-item');

                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
            });

            $(document).ready(function() {
                // Fetch counts
                const endpoints = ['all', 'diterima', 'pengajuan', 'ditolak'];
                endpoints.forEach((status, index) => {
                    $.ajax({
                        url: `/count-data/${status}`,
                        type: 'GET',
                        success: function(response) {
                            $('.btn-wrapper .count').eq(index).text(response.count);
                        }
                    });
                });
            });

            function filterData(status) {
                $.ajax({
                    url: `/data/filter/${status}`,
                    type: 'GET',
                    success: function(response) {
                        $('tbody').html(response);
                    }
                });
            }
        </script>
    @endsection
@endif
