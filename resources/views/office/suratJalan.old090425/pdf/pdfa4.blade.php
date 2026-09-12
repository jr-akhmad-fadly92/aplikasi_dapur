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
  <body style="border: 1px solid black;">
      
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
                  <table class="table-kepada" style="width: 100%;">
                      <tr>
                        <td style="width: 20%;">Nama Sekolah</td>
                        <td style="width: 5%;">:</td>
                        <td style="width: 75%;">nama sekolah</td>
                      </tr>
                      <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>alamat sekolah</td>
                      </tr>
                      
                      
                  </table>
              </td>
              <td style="border-left: 1px solid black; text-align: left; vertical-align: top; padding: 2px; width: 20%;">
                  No. 1/BGN/SJ/03/2025<br>
                  Tanggal: 23 Maret 2025
                  
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
          <th class="border" style="width: 25%; text-align: center;">Jml. Penerimaan</th>
          <th class="border" style="width: 25%; text-align: center;">Jml. Pengembalian*</th>
        </tr>
        
        <tr>
            <td class="border">1</td>
            <td class="border">Porsi A</td>
            <td class="border">100</td>
            <td class="border"></td>
            
        </tr>
        <tr>
            <td class="border">2</td>
            <td class="border">Porsi B</td>
            <td class="border">50</td>
            <td class="border"></td>
            
        </tr>
        <tr>
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
            
        </tr>
      </table>
      <br>
      <table>
        <tr>
            <td style="font-weight:bold;">* Diisi petugas dapur saat pengambilan rantang kotor</td>
        </tr>
      </table>
      <br>
      <table class="table table-setuju" style="width: 100%;">
        <tr>
            <td colspan="2" class="border" style="text-align:center; font-weight: bold;">Penerimaan</td>
            <td colspan="2" class="border" style="text-align:center; font-weight: bold;">Pengembalian</td>
        </tr>
        <tr>
          <td style="text-align: center; width: 25%;">
            Petugas Pengiriman,
            <br>
            <br>
            <br>
            <br>
            <hr style="border: 1px solid black;">
          </td>
          <td style="text-align: center; width: 25%;">
            Pihak Sekolah,
            <br>
            <br>
            <br>
            <br>
            <hr style="border: 1px solid black;">
          </td>
          <td style="border-left: 1px solid black;text-align: center; width: 25%;">
            Petugas Pengambil,
            <br>
            <br>
            <br>
            <br>
            <hr style="border: 1px solid black;">
          </td>
          <td style="text-align: center; width: 25%;">
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
      <br>
      <div class="footer">
        <table class="table" style="width: 50%;">
        <tr>
            <td>Putih: Sekolah</td>
            <td>Merah: BGN</td>
            
          </tr>
        </table>
      </div>
      
  </body>
</html>