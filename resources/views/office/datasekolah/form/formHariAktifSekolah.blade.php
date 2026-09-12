
@if(isset($sekolah_id))
<form id="form_hari_aktif_sekolah" method="post" enctype="multipart/form-data">
    @csrf
    
    <table class="table table-sm table-striped">
        <tr>
            <td>Senin</td>
            <td> : </td>
            <td><input type="checkbox" name="senin" id="senin" class="form-control form-control-sm" value="1" @if (isset($sekolah_aktif))
                @if ($sekolah_aktif->senin == 1) checked 
                @endif
                @endif
                
            ></td>
        </tr>
        <tr>
            <td>Selasa</td>
            <td> : </td>
            <td><input type="checkbox" name="selasa" id="selasa" class="form-control form-control-sm" value="1" @if (isset($sekolah_aktif))
            @if ($sekolah_aktif->selasa == 1) checked 
            @endif
            @endif
                
            ></td>
        </tr>
        <tr>
            <td>Rabu</td>
            <td> : </td>
            <td><input type="checkbox" name="rabu" id="rabu" class="form-control form-control-sm" value="1" @if (isset($sekolah_aktif))
            @if  ($sekolah_aktif->rabu == 1) checked @endif
            @endif
                
            ></td>
        </tr>
        <tr>
            <td>Kamis</td>
            <td> : </td>
            <td><input type="checkbox" name="kamis" class="form-control form-control-sm" value="1" @if (isset($sekolah_aktif))
            @if ($sekolah_aktif->kamis == 1) checked @endif
            @endif
                
            ></td>
        </tr>
        <tr>
            <td>Jumat</td>
            <td> : </td>
            <td><input type="checkbox" name="jumat" class="form-control form-control-sm" value="1" @if (isset($sekolah_aktif))
            @if ($sekolah_aktif->jumat == 1) checked @endif
            @endif
                
            ></td>
        </tr>
        <tr>
            <td>Sabtu</td>
            <td> : </td>
            <td><input type="checkbox" name="sabtu" class="form-control form-control-sm" value="1" @if (isset($sekolah_aktif))
            @if ($sekolah_aktif->sabtu == 1) checked @endif
            @endif
                
            ></td>
        </tr>
        <tr>
            <td>Minggu</td>
            <td> : </td>
            <td><input type="checkbox" name="minggu" class="form-control form-control-sm" value="1" @if (isset($sekolah_aktif))
            @if ($sekolah_aktif->minggu == 1) checked @endif
            @endif
                
            ></td>
        </tr>
    </table>
    
</form>
@else
Data tidak ditemukan
@endif
