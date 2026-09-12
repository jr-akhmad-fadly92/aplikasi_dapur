<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    
    <style>
        .content-title {
            text-align: center;
            width: 100%;
        }
        #table-container table {
            font-size: 24px;
            font-weight: bold;
            width: 100%;
        }

        /* Khusus judul halaman */
        .content-title {
            font-size: 28px;
            font-weight: bold;
        }

        /* Opsi tambahan jika ingin semua teks dalam #table-container besar dan tebal */
        #table-container {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body class="sidebar-mini sidebar-collapse sidebar-closed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            
            
            <!-- Section: Data Kontainer -->
            <section class="content">
                <h1 class="content-title">Bahan Baku Yang Datang Hari ini</h1>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div id="table-container"></div>
                        </div>
                    </div>
                </div>
            </section>
            
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    @include('Template.script')
    
    
    <script>
        let index = 0;
        const id_po = {{ $po }}; // sesuaikan ID PO-nya
        const refreshTime = 10000;

        function loadData() {
            fetch(`/get-po-bahan/${index}`)
                .then(res => {
                    if (res.ok) return res.json();
                    if (res.status === 404) return { notFound: true };
                    return null;
                })
                .then(data => {
                    const container = document.getElementById('table-container');
                    
                    if (data?.notFound) {
                        container.innerHTML = `
                            <div class="card shadow">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Tidak ada kiriman hari ini</h5>
                                </div>
                            </div>
                        `;
                        index = 0; // reset index
                    }
                    else if (data && Object.keys(data).length > 0) {
                        container.innerHTML = `
                            <div class="card shadow">
                                <div class="card-body">
                                    <table class="table table-bordered table-striped align-middle">
                                        <thead class="table-dark text-center">
                                            <tr>
                                                <th>Header</th>
                                                <th>Jumlah</th>
                                                <th>Jumlah Datang</th>
                                                <th>Kurang</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Nomor PO</strong></td>
                                                <td colspan="4"><strong>${data.nomor_po}</strong></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bahan</strong></td>
                                                <td colspan="4">${data.bahan}<br></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jumlah Bahan</strong></td>
                                                <td>${data.jumlah_bahan} ${data.satuan}</td>
                                                <td colspan="2">${data.jumlah_datang || 0} ${data.satuan}</td>
                                                <td>${data.kekurangan} ${data.satuan}</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jumlah Box</strong></td>
                                                <td>${data.jumlah_box} box</td>
                                                <td colspan="3"></td>
                                            </tr>
                                            ${data.detail_box?.map((box, i) => `
                                            <tr>
                                                <td>${i === 0 ? '<strong>Detail Box</strong>' : ''}</td>
                                                <td colspan="4">
                                                    Box ${i + 1}: ${box.isi_per_box ?? 1} ${data.satuan} 
                                                    <span class="badge bg-${box.status === 'sudah datang' ? 'success' : 'warning'}">
                                                        ${box.status}
                                                    </span>
                                                </td>
                                            </tr>
                                            `).join('')}
                                            <tr>
                                                <td><strong>Tanggal Kedatangan</strong></td>
                                                <td colspan="4">
                                                    ${data.tanggal_kedatangan
                                                        ? new Date(data.tanggal_kedatangan).toLocaleString('id-ID', {
                                                            hour: '2-digit',
                                                            minute: '2-digit',
                                                            day: '2-digit',
                                                            month: 'long',
                                                            year: 'numeric'
                                                        })
                                                        : '-'}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Parameter</strong></td>
                                                <td colspan="4">${data.keterangan}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                        // Reset scroll ke atas dulu
                        //window.scrollTo({ top: 0, behavior: 'instant' });

                        // Scroll perlahan-lahan
                        //let scrollStep = 1; // pixel per step
                        //const scrollInterval = 8; // ms per step
                        //const scrollTimer = setInterval(() => {
                          //  const maxScroll = document.body.scrollHeight - window.innerHeight;
                           // if (window.scrollY < maxScroll) {
                             //   window.scrollBy(0, scrollStep);
                           // } else {
                             //   clearInterval(scrollTimer);
                            //}
                        //}, scrollInterval);
                        index++; // ke data berikutnya
                    } else {
                        index = 0; // reset index jika data null
                    }
                })
                .catch(err => {
                    console.error("Gagal mengambil data:", err);
                    index = 0;
                });
        }

        loadData();
        setInterval(loadData, refreshTime);

    </script>

    
</body>
</html>
