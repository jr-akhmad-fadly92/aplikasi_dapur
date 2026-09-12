<form id="form_data_siswa" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="tahun_ajaran">Tahun ajaran</label>
        <input type="text" class="form-control" id="tahun_ajaran" placeholder="Tahun ajaran {{ date('Y') }}/{{ date('Y')+1 }}" name="tahun_ajaran">
    </div>
    <div class="form-group">
        <label for="Semester">Semester</label>
        <select class="form-control" id="semester" name="semester">
            <option value="ganjil">Ganjil</option>
            <option value="genap">Genap</option>
        </select>
    </div>
    <div class="form-group">
        <label for="jumlah_a">Kelompok A</label>
        <input type="number" class="form-control" id="jumlah_a" placeholder="Jumlah Kelompok A" name="jumlah_a" default="0">

    </div>
    <div class="form-group">
        <label for="jumlah_b">Kelompok B</label>
        <input type="number" class="form-control" id="jumlah_b" placeholder="Jumlah Kelompok B" name="jumlah_b" default="0">
    </div>
    
</form>