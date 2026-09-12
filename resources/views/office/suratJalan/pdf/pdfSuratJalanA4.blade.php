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
  <body style="padding: 1.9cm; margin: 0px !important;">
  @php
      $max_page = 1;
      $t_page = ceil(count($surat_jalan_item->toArray())/$max_page);
      
      @endphp
      @for($i = 0; $i < $t_page; $i++)
      @php $start = $i * $max_page; 
      
      @endphp
      @foreach($surat_jalan_item->slice($start, $max_page) as $index => $barang)
      
      <table style="width: 100%;">
        <tr>
          <td style="width: 25%;"></td>
          <td style="width: 50%;">
            <h3 class="bold center">SURAT JALAN
              <br>PROGRAM MAKAN BERGIZI GRATIS
              <br>SPPG YAYASAN BINA BANGSA {{ strtoupper($dapur->kota ?? '') }}
            </h3>
          </td>
          <td style="text-align: right;">
            <img src="{{ public_path('image/logo-ybb.jpg') }}" alt="Logo" style="width: 60px; height: auto;">
            <img src="{{ public_path('image/logo.png') }}" alt="Logo" style="width: 60px; height: auto;">
          </td>
        </tr>
      </table>
      <h3 style="padding-left: 5px;">
        @if (isset($barang->rincianSekolah->data_sekolah->nama_sekolah))
          <span>Nama Sekolah: </span>&nbsp;{{strtoupper($barang->rincianSekolah->data_sekolah->nama_sekolah)}}
        @endif
      </h3>
      <table class="no-border" style="width: 100%;">
        <tr>
          <td style="width: 25%;" class="bold">Tanggal</td>
          <td class="bold" style="width: 2%;">:</td>
          <td>
          {{ \Carbon\Carbon::parse($surat_jalan->published_at)->locale('id')->translatedFormat('l, d F Y') }}  
          </td>
        </tr>
        <tr>
          <td style="width: 15%;" class="bold">Waktu Pengiriman</td>
          <td class="bold" style="width: 2%;">:</td>
          <td>
          ...............................  
          </td>
        </tr>
        <tr>
          <td style="width: 15%;" class="bold">Nama Driver</td>
          <td class="bold" style="width: 2%;">:</td>
          <td>
          @if (isset($surat_jalan->driver))
            {{$surat_jalan->driver}}
            
          @endif  
          </td>
        </tr>
        <tr>
          <td style="width: 15%;" class="bold">Nama Asisten Driver</td>
          <td class="bold" style="width: 2%;">:</td>
          <td>
            @if (isset($surat_jalan->driver_assistant))
              {{$surat_jalan->driver_assistant}}
              
            @endif  
          </td>
        </tr>
        <tr>
          <td style="width: 15%;" class="bold">Plat Mobil</td>
          <td class="bold" style="width: 2%;">:</td>
          <td>
          @if (isset($surat_jalan->plat_nomor))
            {{$surat_jalan->plat_nomor}}
            
          @endif  
          </td>
        </tr>
      </table>
      
      
      
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
          <th class="border" style="width: 5%; text-align: center;" rowspan="2">No.</th>
          <th class="border" style="width: 50%; text-align: center;" rowspan="2">Kelas</th>
          <th class="border" style="width: 25%; text-align: center;" rowspan="2">Jml. Porsi<br>(porsi)</th>
          <th class="border" style="width: 25%; text-align: center;" colspan="2">Jml. Food Tray<br>(porsi)</th>
          <th class="border" style="width: 25%; text-align: center;" rowspan="2">Keterangan</th>
        </tr>
        <tr>
          <th class="border" style="width: 25%; text-align: center;">Sebelum</th>
          <th class="border" style="width: 25%; text-align: center;">Setelah</th>
        </tr>
        
        @if (isset($barang->rincianSekolah->data_sekolah->jenjang_sekolah))
          @if ($barang->rincianSekolah->data_sekolah->jenjang_sekolah == 'SMA/Sederajat')
            <tr>
              <td style="height: 25px;" class="border">1</td>
              <td class="border">Kelas X</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">2</td>
              <td class="border">Kelas XI</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">3</td>
              <td class="border">Kelas XII</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            @php
              $jumlah_kelas = 3;
            @endphp

            @elseif ($barang->rincianSekolah->data_sekolah->jenjang_sekolah == 'SMP/Sederajat')
            <tr>
              <td style="height: 25px;" class="border">1</td>
              <td class="border">Kelas VII</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">2</td>
              <td class="border">Kelas VIII</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">3</td>
              <td class="border">Kelas IX</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            @php
              $jumlah_kelas = 3;
            @endphp
          @elseif ($barang->rincianSekolah->data_sekolah->jenjang_sekolah == 'SD/Sederajat')
            <tr>
              <td style="height: 25px;" class="border">1</td>
              <td class="border">Kelas I</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">2</td>
              <td class="border">Kelas II</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">3</td>
              <td class="border">Kelas III</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">4</td>
              <td class="border">Kelas IV</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">5</td>
              <td class="border">Kelas V</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            <tr>
              <td style="height: 25px;" class="border">6</td>
              <td class="border">Kelas VI</td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            @php
              $jumlah_kelas = 6;
            @endphp
            @endif
            @for ($a= 0; $a < 10; $a++)
            <tr>
              <td style="height: 25px;" class="border">{{$a+1}}</td>
              <td class="border"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border center"></td>
              <td class="border"></td>
            </tr>
            @endfor
            
            
         
          
        @endif
        
      </table>
      
      <br>
      <table class="table table-setuju" style="width: 100%;">
        
        <tr>
          <td style="text-align: center; width: 33%;">
            Ahli Gizi,
            <br>
            <br>
            <br>
            <br>
            .............................................
          </td>
          <td style="text-align: center; width: 33%;">
            Assisten Lapangan,
            <br>
            <br>
            <br>
            <br>
            .............................................
          </td>
          <td style="text-align: center; width: 33%;">
            Pihak Sekolah,
            <br>
            <br>
            <br>
            <br>
            .............................................
          </td>
          
        </tr>
      </table>
      <br>
      <div class="footer">
        <table class="table" style="width: 100%;">
        <tr>
            <td class="bold">*wajib dengan nama terang beserta stempel basah sekolah</td>
            
            
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