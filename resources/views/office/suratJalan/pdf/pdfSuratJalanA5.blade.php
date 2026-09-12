<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Surat Jalan</title>
    <!-- Bootstrap -->
    <style>
      @page{
        
        font-size: 13.5px;
        margin-left: 20px;
        margin-right: 10px;
        margin-top: 15px;
        margin-bottom: 15px;
        }
      
        .page-break {
        page-break-after: always;
        }
        .table{
            border-collapse: collapse;
            width: 90%;
            vertical-align: top;
        }
        .table-head, .table-head>tr, .table-head>td{
            border: 1px solid black;
        }
        .table-kepada td{
            vertical-align: top;
        }
        .table-penerima, .table-penerima tr {
            border: 1px solid black;
        }
        .bold{
          font-weight: bold;
        }
        .center{
          text-align: center;
        }
        .right{
          text-align: right;
        }

    </style>
  </head>
  <body style="border: 2px solid black; padding: 2px; margin: 0px !important;">
  @php
      $max_page = 1;
      $t_page = ceil(count($surat_jalan_item->toArray())/$max_page);
      @endphp
      @for($i = 0; $i < $t_page; $i++)
      @php $start = $i * $max_page; 
      
      @endphp
      @foreach($surat_jalan_item->slice($start, $max_page) as $index => $barang)
      <h3 class="bold center">SURAT JALAN</h3>
      <table class="no-border" style="width: 100%;">
          <tr>
              <td><strong>Nama Dapur </strong></td>
              <td>: {{$dapur->nama_dapur}}</td>
              <td><strong>Tanggal Pengiriman</strong> </td>
              <td>: {{date('d F Y',strtotime($surat_jalan->published_at))}}</td>
          </tr>
          <tr>
              <td><strong>ID Dapur</strong> </td>
              <td>: {{$dapur->nomor_dapur}} </td>
              <td class="bold">Armada</td>
              <td>: {{$surat_jalan->plat_nomor}}</td>
          </tr>
          
          <tr>
              <td ><strong>Alamat Dapur</strong></td>
              <td>:  {{$dapur->alamat_dapur}} </td>
              <td class="bold"></td>
              <td class="bold"></td>
          </tr>
        
      </table>
      <br>
      
      <table class="table table-head " style="width: 100%;">
          <tr>
              <td style="width: 80%;">
                  <table class="table-kepada" style="width: 100%;">
                      <tr>
                        <td style="width: 20%;">Nama Sekolah</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 75%;">{{strtoupper($barang->rincianSekolah->data_sekolah->nama_sekolah)}}</td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{$barang->rincianSekolah->data_sekolah->alamat_sekolah}}</td>
                      </tr>
                      
                      
                  </table>
              </td>
              <td style="border-left: 1px solid black; text-align: left; vertical-align: top; padding: 2px; width: 20%;">
                    No. {{$surat_jalan->no_surat_jalan}}<br>
                  Tanggal: {{date('d F Y',strtotime($surat_jalan->published_at))}}
                  
              </td>
          </tr>
      </table>
      <br>
      
      <style>
        .table-item{
          border-collapse: collapse;
        }
        .table-item td{
          padding: 2px;
          
        }
        .border{
          border: 1px solid black;
        }
        .center{
          text-align: center;
        }
        .right{
          text-align: right;
        }
        
      </style>
      <table class="table-item" style="width: 100%; border: 1px solid black;">
        <tr>
          <th class="border" style="width: 5%; text-align: center;">No.</th>
          <th class="border" style="width: 50%; text-align: center;">Rincian</th>
          <th class="border" style="width: 25%; text-align: center;">Jml. Penerimaan<br>(porsi)</th>
          <th class="border" style="width: 25%; text-align: center;">Jml. Pengembalian*<br>(porsi)</th>
        </tr>
        
        <tr>
            <td class="border">1</td>
            <td class="border">Porsi A</td>
            <td class="border right">{{$barang->jumlah_a}}</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">2</td>
            <td class="border">Porsi B</td>
            <td class="border right">{{$barang->jumlah_b}}</td>
            <td class="border"></td>
            
        </tr>
        <!--<tr>
            <td class="border">3</td>
            <td class="border">Porsi C</td>
            <td class="border">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">4</td>
            <td class="border">Porsi D</td>
            <td class="border">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">5</td>
            <td class="border">Porsi E</td>
            <td class="border">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">6</td>
            <td class="border">Porsi F</td>
            <td class="border">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">7</td>
            <td class="border">Porsi G</td>
            <td class="border">0</td>
            <td class="border"></td>
            
        </tr>-->
      </table>
      <br>
      <table>
        <tr>
            <td style="font-weight:bold;">* Diisi petugas dapur saat pengambilan tempat makan kotor</td>
        </tr>
      </table>
      <br>
      <table class="table table-setuju" style="width: 100%;">
        <tr>
            <td colspan="2" class="border" style="text-align:center; font-weight: bold;">Office</td>
            
            <td colspan="2" class="border" style="text-align:center; font-weight: bold;">Penerimaan</td>
            <td colspan="2" class="border" style="text-align:center; font-weight: bold;">Pengembalian</td>
        </tr>
        <tr>
          <td style="text-align: center; width: 18%;">
            SPPI,
            <br>
            <br>
            <br>
            <br>
            
            <hr style="border: 1px solid black;">
          </td>
          <td style="text-align: center; width: 18%;">
            Asisten Lapangan,
            <br>
            <br>
            <br>
            <br>
            <hr style="border: 1px solid black;">
          </td>
          <td style="border-left: 1px solid black;text-align: center; width: 17%;">
            Petugas Pengiriman,
            <br>
            <br>
            <br>
            
            {{$surat_jalan->driver}}
            <hr style="border: 1px solid black;">
          </td>
          <td style="text-align: center; width: 17%;">
            Pihak Sekolah,
            <br>
            <br>
            <br>
            <br>
            <hr style="border: 1px solid black;">
          </td>
          <td style="border-left: 1px solid black;text-align: center; width: 15%;">
            Petugas Pengambil,
            <br>
            <br>
            <br>
            <br>
            <hr style="border: 1px solid black;">
          </td>
          <td style="text-align: center; width: 15%;">
            Pihak Sekolah,
            <br>
            <br>
            <br>
            <br>
            <hr style="border: 1px solid black;">
          </td>
        </tr>
      </table>
      <br>
      <div class="footer">
        <table class="table" style="width: 50%;">
        <tr>
            <td>Putih: Sekolah</td>
            <td>Merah: BGN</td>
            
          </tr>
        </table>
      </div>
      @endforeach
      @if($i+1 < $t_page)
      <div class="page-break"></div>
      @endif
      @endfor
  </body>
</html>