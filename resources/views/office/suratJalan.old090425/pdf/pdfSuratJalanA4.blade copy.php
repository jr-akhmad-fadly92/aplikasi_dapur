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
        .table-penerima, .table-penerima tr, {
            border: 1px solid black;
        }
    </style>
  </head>
  <body style="border: 2px solid black; padding: 2px; margin: 0px !important;">
  @php
      $max_page = 5;
      $t_page = ceil(count($surat_jalan_item->toArray())/$max_page);
      @endphp
      @for($i = 0; $i < $t_page; $i++)
      @php $start = $i * $max_page; @endphp
      <table class="table">
          <tr>
              <th style="width: 80px;">
                  <img src="{{asset('image/logo.png')}}" class="img" style="width: 50px;">
              </th>
              <th style="text-align: left; width: 400px; vertical-align: middle; font-size: 20px;">
                Badan Gizi Nasional<br>
                
              </th>
              
          </tr>
          <tr>
            <td colspan="2" style="text-align:center; font-size:15px; font-weight: bold;">Surat Jalan</td>
          </tr>
      </table>
      <br>
      
      <table class="table table-head " style="width: 100%;">
          <tr>
              <td style="width: 80%;">
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
          <th class="border" style="width: 25%; text-align: center;">Lokasi</th>
          <th class="border" style="width: 15%; text-align: center;">Rincian</th>
          <th class="border" style="width: 25%; text-align: center;" colspan="2">Jml. Penerimaan</th>
          <th class="border" style="width: 25%; text-align: center;" colspan="2">Jml. Pengembalian*</th>
        </tr>
      @foreach($surat_jalan_item->slice($start, $max_page) as $index => $barang)
        
        <tr>
            <td class="border" rowspan="7">{{$index+1}}</td>
            <td class="border" rowspan="7" style="vertical-align: top;">
              <b>Nama Sekolah:</b> {{strtoupper($barang->rincianSekolah->data_sekolah->nama_sekolah)}}<br>
              <b>Alamat:</b> {{$barang->rincianSekolah->data_sekolah->alamat_sekolah}}
            </td>
            <td class="border">Porsi A</td>
            <td class="border" style="width: 5%; text-align: right;">{{$barang->jumlah_a}}</td>
            <td class="border" rowspan="7" style="vertical-align: top;">
              Ttd. Petugas<br><br><br><br><hr>
              Ttd. penerima
            </td>
            <td class="border" style="width: 5%; tex-align: right;"></td>
            <td class="border" rowspan="7" style="vertical-align: top;">
              Ttd. Petugas<br><br><br><br><hr>
              Ttd. penerima
            </td>
            
        </tr>
        <tr>
            <td class="border">Porsi B</td>
            <td class="border" style="text-align:right;">{{$barang->jumlah_a}}</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">Porsi C</td>
            <td class="border" style="text-align:right;">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">Porsi D</td>
            <td class="border" style="text-align:right;">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">Porsi E</td>
            <td class="border" style="text-align:right;">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">Porsi F</td>
            <td class="border" style="text-align:right;">0</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">Porsi G</td>
            <td class="border" style="text-align:right;">0</td>
            <td class="border"></td>
            
        </tr>
      @endforeach

      </table>
      <br>
      <table>
        <tr>
            <td style="font-weight:bold;">* Diisi petugas dapur saat pengambilan rantang kotor</td>
        </tr>
      </table>
      <br>
      
     
      @if($i+1 < $t_page)
      <div class="page-break"></div>
      @endif
      @endfor
  </body>
</html>