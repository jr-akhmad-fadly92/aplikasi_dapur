
<form id="form_transaksi_barang_masuk" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="kebutuhan_beras">kebutuhan beras</label>
        <input type="number" class="form-control form-control-sm" readonly placeholder="kebutuhan_beras" value="@if(isset($kebutuhan_beras)){{$kebutuhan_beras}}@else{{0}}@endif" name="kebutuhan_beras" id="kebutuhan_beras">
    </div>
    <div class="form-group">
        <label for="tray">Tray</label>
        <input type="number" class="form-control form-control-sm" placeholder="Nama barang" value="{{ $tray }}" name="tray" id="tray" >
         
    </div>
    <div class="form-group">
        <label for="nama_barang">Nama Barang</label>
        <input type="text" class="form-control form-control-sm" placeholder="Nama barang" value="{{ $data->bahan}}" name="nama_barang" id="nama_barang" readonly>
        <input type="text" class="form-control form-control-sm" placeholder="Nama barang" value="{{ $data->id_penerimaan}}" name="nama_barang_po" id="nama_barang_po" readonly hidden>
       
    </div>
    <div class="form-group">
        <label for="jumlah">Jumlah Berat Bahan</label>
        <input type="number" class="form-control form-control-sm"  placeholder="Jumlah barang" value="{{ $data->jumlah_datang}}" name="jumlah" id="jumlah" readonly>
    </div>
    <div class="form-group">
        <label for="id_satuan">Satuan</label>
        <select name="id_satuan" id="id_satuan" class="form-control form-control-sm " readonly>
            @foreach ($satuan as $satuan)
                <option value="{{$satuan->id}}" @if($satuan->id == $data->satuan_bahan) selected @endif >{{$satuan->satuan}}</option>
            @endforeach
        </select>
       
        </select>
    </div>
    <div class="form-group">
        <label for="tanggal_akan_keluar">Tanggal akan dikeluarkan</label>
        <input type="text" class="form-control form-control-sm datepicker" id="tanggal_akan_keluar" name="tanggal_akan_keluar" value="{{ \Carbon\Carbon::parse($data->tanggal_digunakan)->format('d/m/Y') }}">
    </div>
    <div class="form-group">
        <label for="lokasi">Lokasi Penyimpanan</label>
        <select name="lokasi" name="lokasi" class="form-control form-control-sm">
            <option value="gudang_kering">Gudang Kering</option>
            <option value="gudang_chiller">Chiller</option>
            <option value="gudang_freezer">Freezer</option>
        </select>
    </div>
    <div class="form-group">
        <label for="jenis">Jenis Barang</label>
        <select name="jenis" name="jenis" class="form-control form-control-sm">
            <option value="1">Bahan masak</option>
            <option value="2">Peralatan</option>
        </select>
    </div>
    <div class="form-group">
        <label for="keterangan">keterangan</label>
        <textarea class="form-control form-control-sm" name="keterangan" id="keterangan" rows="3" placeholder="Keterangan"></textarea>
        
    </div>
    <div class="form-group" hidden>
        <label for="jumlah">X barang masuk</label>
        <input type="number" class="form-control form-control-sm"  placeholder="Jumlah barang" value="1" name="loop_kontainer" id="loop_kontainer">
    </div>
</form>
<style>
.daterangepicker {
    z-index: 99999999 !important; /* Lebih tinggi dari modal Bootstrap (1050) */
}
</style>

<script>
$(document).ready(function() {
    $('.datepicker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY' // Format tanggal
        }
    });
});
</script>
<script>
$(document).ready(function() {
        $('#no_po').on('input', function() {
        var nomor_po = $(this).val().trim();

        if (nomor_po !== "") {
            $.ajax({
                url: "/get-bahan-by-po",
                type: "POST",
                data: {
                    nomor_po: nomor_po,
                    jumlah_masuk : 0,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.length > 0) {
                        $('#nama_barang').hide();
                        $('#nama_barang_po').show().empty();
                       // $('select[name="id_satuan"]').hide();
                       // $('#id_satuan_po').show().empty();
                        $.each(response, function(index, item) {
                            $('#nama_barang_po').append(
                                '<option value="' + item.id + '" data-jumlah="' + item.jumlah_datang + '">' + 
                                item.bahan + ' || ' + item.jumlah_datang + ' ' + item.satuan + 
                                '</option>'
                            );
                        });

                        //var firstJumlahDatang = response[0].jumlah_datang || 0;
                        //var firstJumlahDatang = jumlah_masuk || 0;
                       // $('#jumlah').val(0);
                       let firstJumlah = $('#nama_barang_po option:selected').data('jumlah') || 0;
                        $('#jumlah').val(firstJumlah);

                        // Ketika pilihan diubah, update #jumlah
                        $('#nama_barang_po').on('change', function() {
                            var jumlah = $(this).find(':selected').data('jumlah') || 0;
                            $('#jumlah').val(jumlah);
                        });

                       
                    } else {
                        $('#nama_barang_po').hide();
                        $('#nama_barang').show();

                        $('#id_satuan_po').hide();
                        $('select[name="id_satuan"]').show();
                    }
                }
            });
        } else {
            $('#nama_barang_po').hide();
            $('#nama_barang').show();
             $('#id_satuan_po').hide();
            $('select[name="id_satuan"]').show();
        }
    });
});
</script>
<script>
    $('.select2').select2({
        dropdownCssClass: "increasedzindexclass",
        
    });
    
</script>
<style>
	.select2-dropdown.increasedzindexclass {
	  z-index: 99999999;
	}
    .select2-container .select2-selection--single {
            height: 40px !important; /* Samakan dengan input lainnya */
            padding: 5px;
            display: flex;
            align-items: center;
        }
        
        .select2-selection__rendered {
            line-height: 30px !important;
        }
</style>

