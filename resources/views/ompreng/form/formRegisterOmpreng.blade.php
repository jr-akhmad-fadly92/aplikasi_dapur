<form id="form_register_ompreng" method="post" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label for="kode_ompreng">Scan Kode Opreng/Rantang</label>
        <input type="text" class="form-control " placeholder="Scan kode ompreng" value="" name="kode_ompreng" id="kode_ompreng" autofocus autocomplete="off">
    </div>
    
    
    
</form>
<br>
<script>
    $(document).on('submit', '#form_register_ompreng', function(e) {
    e.preventDefault(); // Hentikan form agar tidak redirect
    //simpanOmpreng();    // Jalankan AJAX
    formRegisterNomorOmpreng();
});
</script>
