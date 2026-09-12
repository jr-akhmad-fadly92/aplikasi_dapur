<form id="form_register_nomor_ompreng" method="post" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label for="kode_ompreng">Masukkan No. Ompreng</label>
        <input type="text" class="form-control " placeholder="input nomor ompreng" value="" name="nomor" id="nomor" autofocus autocomplete="off">
    </div>
    
    
    
</form>
<br>
<script>
    $(document).on('submit', '#form_register_nomor_ompreng', function(e) {
    e.preventDefault(); // Hentikan form agar tidak redirect
    simpanOmpreng();    // Jalankan AJAX
});
</script>
