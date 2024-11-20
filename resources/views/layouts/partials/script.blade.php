<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- plugins:js -->
{{-- <script src="{{ asset('skydash/vendors/js/vendor.bundle.base.js') }}"></script> --}}
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{ asset('skydash/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('skydash/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('skydash/js/dataTables.select.min.js') }}"></script>

<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{ asset('skydash/js/off-canvas.js') }}"></script>
<script src="{{ asset('skydash/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('skydash/js/template.js') }}"></script>
<script src="{{ asset('skydash/js/settings.js') }}"></script>
<script src="{{ asset('skydash/js/todolist.js') }}"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{ asset('skydash/js/dashboard.js') }}"></script>
<script src="{{ asset('skydash/js/Chart.roundedBarCharts.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- End custom js for this page-->
@if (session('success'))
    <script>
        Swal.fire({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success"
        });
    </script>
@endif


