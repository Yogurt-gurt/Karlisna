@extends('layouts.dashboard-layout')

@section('content')

<head>
    <title>File Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="filters">
            <div>
                <select>
                    <option>Pilih Periode</option>
                </select>
                <select>
                    <option>Diinput Oleh</option>
                </select>
                <select>
                    <option>Title</option>
                </select>
            </div>
            <div>
                <button><i class="fas fa-download"></i> Unduh Template</button>
                <button type="button" data-toggle="modal" data-target="#addNewsModal"><i class="fas fa-plus"></i> Tambah Berita</button>
            </div>
        </div>

        <!-- Modal Tambah Berita -->
        <div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Header Modal -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewsModalLabel">Tambah Berita</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Body Modal -->
                    <div class="modal-body">
                        <form action="{{ route('create-news') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Judul Input -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                            </div>

                            <!-- Deskripsi Input -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control" required>{{ old('deskripsi') }}</textarea>
                            </div>

                            <!-- Tanggal Unggah Input -->
                            <div class="mb-3">
                                <label for="upload_date" class="form-label">Tanggal Unggah <span class="text-danger">*</span></label>
                                <input type="date" name="upload_date" id="upload_date" class="form-control" value="{{ old('upload_date') }}" required>
                            </div>

                            <!-- Diinput oleh Input -->
                            <div class="mb-3">
                                <label for="uploaded_by" class="form-label">Diinput oleh <span class="text-danger">*</span></label>
                                <input type="text" name="uploaded_by" id="uploaded_by" class="form-control" placeholder="Masukkan nama penginput" value="{{ old('uploaded_by') }}" required>
                            </div>

                            <!-- File Input -->
                            <div class="mb-3">
                                <label for="file" class="form-label">File</label>
                                <input type="file" name="file" id="file" class="form-control">
                            </div>

                            <!-- Footer Modal -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn custom-blue-button ml-2">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- <a href="{{ route('kelola-news') }}">Daftar Berita</a>
 -->


        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Nama Berkas</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Unggah <i class="fas fa-sort-down"></i></th>
                        <th>Diinput oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newsItems as $index => $newsItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $newsItem->title }}</td>
                        <td>
                            @if($newsItem->nama_berkas)
                            @php
                            $displayName = preg_replace('/^\d+_/', '', $newsItem->nama_berkas);
                            @endphp
                            <a href="{{ url('uploads/files/' . $newsItem->nama_berkas) }}" download>{{ $displayName }}</a>
                            @else
                            Tidak ada berkas
                            @endif
                        </td>
                        <td>{{ Str::limit($newsItem->deskripsi, 50) }}</td>
                        <td>{{ $newsItem->upload_date }}</td>
                        <td>{{ $newsItem->uploaded_by }}</td>
                        <td>
                            <a href="#" class="action-icons" data-toggle="modal" data-target="#detailModal{{ $newsItem->id }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="action-icons" data-toggle="modal" data-target="#editModal{{ $newsItem->id }}"><i class="fas fa-edit edit"></i></a>
                            <a href="#" class="action-icons delete-btn" data-id="{{ $newsItem->id }}">
                                <i class="fas fa-trash delete"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach ($newsItems as $index => $newsItem)
        <!-- Modal detail berita -->
        <div class="modal fade" id="detailModal{{ $newsItem->id }}" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Berita</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Nama Berkas</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal Unggah <i class="fas fa-sort-down"></i></th>
                                    <th>Diinput oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $newsItem->title }}</td>
                                    <td>
                                        @if($newsItem->nama_berkas)
                                        @php
                                        $displayName = preg_replace('/^\d+_/', '', $newsItem->nama_berkas);
                                        @endphp
                                        <a href="{{ url('uploads/files/' . $newsItem->nama_berkas) }}" download>{{ $displayName }}</a>
                                        @else
                                        Tidak ada berkas
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($newsItem->deskripsi, 50) }}</td>
                                    <td>{{ $newsItem->upload_date }}</td>
                                    <td>{{ $newsItem->uploaded_by }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @foreach ($newsItems as $index => $newsItem)
        <!-- Modal Edit Anggota -->
        <!-- Modal Edit News -->
        <div class="modal fade" id="editModal{{ $newsItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit News</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editForm" action="{{ route('news.update', $newsItem->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">

                            <!-- Title Input -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ $newsItem->title }}" required>
                            </div>
                            <!-- Description Input -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Description</label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control">{{ $newsItem->deskripsi }}</textarea>
                            </div>
                            <!-- Upload Date Input -->
                            <div class="mb-3">
                                <label for="upload_date" class="form-label">Upload Date <span class="text-danger">*</span></label>
                                <input type="date" name="upload_date" id="upload_date" class="form-control" value="{{ $newsItem->upload_date }}" required>
                            </div>
                            <!-- Uploaded By Input -->
                            <div class="mb-3">
                                <label for="uploaded_by" class="form-label">Uploaded By <span class="text-danger">*</span></label>
                                <input type="text" name="uploaded_by" id="uploaded_by" class="form-control" value="{{ $newsItem->uploaded_by }}" required>
                            </div>
                            <!-- Nama Berkas (File) Display -->
                            <!-- <div class="mb-3">
                                <label for="file" class="form-label">Upload New File</label>
                                <input type="file" name="file" id="file" class="form-control">
                                @if($newsItem->nama_berkas)
                                @php
                                $displayName = preg_replace('/^\d+_/', '', $newsItem->nama_berkas);
                                @endphp
                                <p>
                                    <a href="{{ url('uploads/files/' . $newsItem->nama_berkas) }}" download>{{ $displayName }}</a>
                                </p>
                                @else
                                <p>No file uploaded</p>
                                @endif -->

                            <div>
                                <label for="file" class="form-label mt-2">Upload New File</label>
                                <input type="file" name="file" id="file" class="form-control">
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn custom-blue-button ml-2">Update</button>
                </div>
                </form>
            </div>
        </div>

        @endforeach
        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</body>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap & jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script>
    function openEditModal(newsId) {
        $.ajax({
            url: `/news/${newsId}/edit`,
            method: 'GET',
            success: function(data) {
                $('#editForm').attr('action', `/news/${newsId}`); // Update action URL with ID
                $('#title').val(data.title);
                $('#deskripsi').val(data.deskripsi);
                $('#upload_date').val(data.upload_date);
                $('#uploaded_by').val(data.uploaded_by);
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Failed to load data');
            }
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtns = document.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function(event) {
                event.preventDefault(); // Mencegah aksi default dari tautan

                const id = this.getAttribute('data-id'); // Ambil ID anggota
                const confirmMessage = "Apakah Anda yakin ingin menghapus anggota ini?"; // Pesan konfirmasi

                // Menampilkan SweetAlert2
                Swal.fire({
                    title: "Hapus Anggota?",
                    text: confirmMessage,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Buat form untuk penghapusan
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ url('admin') }}/" + id + "/delete"; // Ganti url sesuai rute Anda

                        const csrfField = document.createElement('input');
                        csrfField.type = 'hidden';
                        csrfField.name = '_token';
                        csrfField.value = "{{ csrf_token() }}"; // Tambahkan token CSRF

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE'; // Metode penghapusan

                        form.appendChild(csrfField);
                        form.appendChild(methodField);

                        document.body.appendChild(form);
                        form.submit(); // Kirim form
                    }
                });
            });
        });
    });
</script>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


@endsection