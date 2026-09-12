<script src="{{ asset('AdminLte/plugins/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('AdminLte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('AdminLte/dist/js/adminlte.min.js') }}"></script>
  <!-- DataTables -->
  <script src="{{ asset('AdminLte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
  
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    function updateTime() {
      var now = new Date();
      var day = now.getDate();
      var month = now.getMonth(); // Ambil angka bulan (0-11)
      var year = now.getFullYear();
      var hours = now.getHours();
      var minutes = now.getMinutes();
      var seconds = now.getSeconds();

      // Array nama bulan
      var months = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
      ];

      if (day < 10) day = "0" + day;
      if (hours < 10) hours = "0" + hours;
      if (minutes < 10) minutes = "0" + minutes;
      if (seconds < 10) seconds = "0" + seconds;

      // Mengambil nama bulan berdasarkan indeks yang didapat dari getMonth()
      var timeString = day + " " + months[month] + " " + year + " " + hours + ":" + minutes + ":" + seconds;

      document.getElementById('currentTime').innerHTML = timeString;
    }

    setInterval(updateTime, 1000);
    updateTime();
  });

  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
 <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
     <script>
        //message with sweetalert
        @if(session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif(session('error'))
            Swal.fire({
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif

    </script>

