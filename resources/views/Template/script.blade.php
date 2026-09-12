  <script src="{{ asset('AdminLte/plugins/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('AdminLte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('AdminLte/dist/js/adminlte.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('AdminLte/plugins/select2/js/select2.full.min.js') }}"></script>
  <!-- DataTables -->
  <script src="{{ asset('AdminLte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/datatables-select/js/dataTables.select.min.js') }}"></script>
  

  <!--jquery confirm-->
  <script src="{{ asset('vendors/jquery-confirm/jquery-confirm.min.js') }}"></script>

  <!--jquery orgchart-->
  <script src="{{ asset('vendors/OrgChart-master/jquery.orgchart.js') }}"></script>
  
  <!--bootstrap switch-->
  <script src="{{ asset('AdminLte/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>

  <!-- date-range-picker -->
  <script src="{{ asset('AdminLte/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('AdminLte/plugins/daterangepicker/daterangepicker.js') }}"></script>
  
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
    <script>
      let prevPoCount = 0;
    
      function loadPONotifications() {
        fetch('/api/po-notif')
          .then(response => response.json())
          .then(data => {
            const menu = document.getElementById('po-notif-menu');
            const count = document.getElementById('po-notif-count');
            const audio = document.getElementById('po-sound');
            menu.innerHTML = '';
    
            // Jika ada data baru dan jumlah bertambah → mainkan suara
            if (data.length > prevPoCount) {
              audio.play();
            }
    
            prevPoCount = data.length;
    
            if (data.length > 0) {
              count.innerText = data.length;
              data.forEach(po => {
                const item = `
                  <a href="#" class="dropdown-item">
                    <div class="media">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">PO: ${po.nomor_po}</h3>
                        <p class="text-sm">Status: Belum tutup</p>
                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> ${new Date(po.tanggal_po).toLocaleDateString()}</p>
                      </div>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                `;
                menu.insertAdjacentHTML('beforeend', item);
              });
    
              menu.insertAdjacentHTML('beforeend', `<a href="/Rekap_po" class="dropdown-item dropdown-footer">Lihat Semua PO</a>`);
            } else {
              count.innerText = 0;
              menu.innerHTML = `<span class="dropdown-item text-center">Tidak ada PO baru</span>`;
            }
          })
          .catch(error => {
            console.error('Gagal load notifikasi PO:', error);
          });
      }
    
      document.addEventListener('DOMContentLoaded', function () {
        loadPONotifications();
        setInterval(loadPONotifications, 10000); // Tiap 1 menit
      });
    </script>
    <!--script>
    document.addEventListener("DOMContentLoaded", function () {
        // Fungsi untuk kirim request
        function sendDailyData() {
            fetch("{{ url('/kirim-menu') }}", {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response:", data);
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }

        // Jalankan pertama kali saat load
        sendDailyData();

        // Ulangi tiap 1 menit (60000 ms)
        setInterval(sendDailyData, 300000);
    });
    </!--script-->


    
    

