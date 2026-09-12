<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }
    table {
        width: 100%;
        text-align: center;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
        text-align: center;
    }
</style>


    <table >
        <tr>
            <th >No</th>
            <th >Laporan</th>
            <th >Total Biaya</th>
            <th colspan="2">Download Form Pengajuan</th>
            <th colspan="4">Status</th>
            <th colspan="4">Aksi</th>
        </tr>
        <tr>
            <th  >1</th>
            <th  >Bantuan Bahan Baku</th>
            <th  >{{ number_format($bantuan_bahan_baku??0,0,',','.') }}</th>
            <th colspan="2"><button onclick="downloadLaporanBahanBaku()" class="btn btn-primary btn-xs "> Download </button></th>
            <th colspan="4">Status</th>
            <th colspan="4">Aksi</th>
        </tr>
        <tr>
            <th  >2</th>
            <th  >Bantuan Operasional ( Non Bahan Baku)</th>
            <th  >{{ number_format(($bantuan_operasional_dengan_po+$bantuan_operasional_non_po_jumlah)??0,0,',','.') }}</th>
            <th colspan="2"><button onclick="downloadLaporanOPnonBB()" class="btn btn-primary btn-xs "> Download </button></th>
            <th colspan="4">Status</th>
            <th colspan="4">Aksi</th>
        </tr>
        <tr>
            <th  >3</th>
            <th  >Bantuan Operasional ( Gaji Relawan )</th>
            <th  >{{ number_format(($bantuan_gaji_karyawan)??0,0,',','.') }} </th>
            <th colspan="2"><button onclick="downloadLaporanGaji()" class="btn btn-primary btn-xs "> Download </button></th>
            <th colspan="4">Status</th>
            <th colspan="4">Aksi</th>
        </tr>
        <tr>
            <th  >4</th>
            <th  >Bantuan Infrastruktur dan Peralatan</th>
            <th  >{{ number_format(($bantuan_infra)??0,0,',','.') }} </th>
            <th colspan="2"><button onclick="downloadLaporanInfra()" class="btn btn-primary btn-xs "> Download </button></th>
            <th colspan="4">Status</th>
            <th colspan="4">Aksi</th>
        </tr>    

            
            
    </table>

