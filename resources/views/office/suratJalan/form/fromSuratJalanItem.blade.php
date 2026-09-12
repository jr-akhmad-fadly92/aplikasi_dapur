

<form id="form_surat_jalan_item" method="post" enctype="multipart/form-data">
    @csrf
    <table class="table table-striped table-sm">
        <tr>
            <th>No</th>
            <th>Nama Sekolah</th>
            <th>Penerima A</th>
            <th>Penerima B</th>
        </tr>
        @if (count($sekolah) < 1)
        <tr>
            <td colspan="4" style="text-align: center;"><h4>Silahkan pilih data terlebih dahulu</h4></td>
        </tr>
        @else 

        @foreach($sekolah as $index => $sekolah)
            
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    {{ $sekolah->nama_sekolah }}
                    <input type="hidden" name="items[{{ $sekolah->id }}][rincian_sekolah_id]" value="{{ $sekolah->id }}">
                </td>
                <td>
                    <input type="number" name="items[{{ $sekolah->id }}][jumlah_penerima_a]" value="{{ $sekolah->jumlah_a }}" class="form-control form-control-sm" min="0" max="{{ $sekolah->jumlah_a }}" >
                </td>
                <td>
                    <input type="number" name="items[{{ $sekolah->id }}][jumlah_penerima_b]" value="{{ $sekolah->jumlah_b }}" class="form-control form-control-sm" min="0" max="{{ $sekolah->jumlah_b }}" >
                </td>
            </tr>

        @endforeach
        @endif

    </table>
</form>
