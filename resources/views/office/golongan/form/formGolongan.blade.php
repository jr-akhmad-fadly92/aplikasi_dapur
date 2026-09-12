<form id="form_golongan" method="post" enctype="multipart/form-data">
    @csrf
    @if(isset($golongan))
    <div class="form-group">
        <label for="id">ID</label>
        <input type="text" class="form-control" value="{{$golongan->id}}" name="id" id="id" readonly>
    </div>
    @endif
    <div class="form-group">
        <label for="parent_id">Parent</label>
        <select class="select2-form form-control" name="parent_id" id="parent_id" style="width: 100%;">
            <option selected value="" disabled>Pilih parent</option>
            @foreach($parent_golongan as $parent_golongan)
            <option value="{{$parent_golongan->id}}" 
            @if(isset($golongan)) 
                @if($golongan->parent_id == $parent_golongan->id) selected @endif 
            @elseif (isset($child_golongan_id))
                @if($parent_golongan->id == $child_golongan_id) selected @endif 
            @endif
            >{{$parent_golongan->golongan}}</option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <label for="no">Nomor sub</label>
        <input type="text" class="form-control " placeholder="Nomor sub jika ada" value="" name="no" id="no" readonly>
    </div>
    <div class="form-group">
        <label for="golongan">Nama golongan</label>
        <input type="text" class="form-control " placeholder="Nama golongan" 
            @if(isset($golongan))
            value="{{$golongan->golongan}}"
            @endif 
        name="golongan" id="golongan">
    </div>
    <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <textarea name="keterangan" id="keterangan" class="form-control " placeholder="Keterangan">@if(isset($golongan)){{$golongan->keterangan}}@endif</textarea>
    </div>
    
</form>
<br>
<script>
    $('.select2-form').select2({
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

