<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
        text-align: center;
    }
</style>

@if (isset($menu) && $menu->isNotEmpty()) 
    <table>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Menu</th>
            <th rowspan="2">Nama Masakan</th>
            <th rowspan="2">Nama Bahan Baku</th>
            <th colspan="2">Kebutuhan</th>
            <th colspan="4">Penerimaan</th>
            <th colspan="4">Persiapan</th>
        </tr>
        <tr>
            <th>qty</th>
            <th>satuan</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Keb. Box</th>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Keb. Box</th>
            <th>Keterangan</th>
        </tr>
        @php
            $lastMenu = '';
            $lastMasakan = '';
            $makananNow = '';
            $lastBahan = '';
            $no = 1;
            $lastNo = 0;
        @endphp
        @foreach ($menu as $m)
        @if($m->rincianMenuKarbohidrat )
                @foreach ($m->rincianMenuKarbohidrat as $bahan)
                    @foreach ($bahan->tbPoBahan as $poBahan)  <!-- Loop untuk tbPoBahan -->
                        @foreach ($poBahan->tbPenerimaan as $penerimaan) <!-- Loop untuk tbPenerimaan -->
                            @foreach ($penerimaan->warehouseTransaksi as $index => $transaksi) <!-- Loop untuk warehouseTransaksi -->
                                
                                <tr>
                                    <td>
                                        @if (isset($m->rincianMenuKarbohidrat->first()->bahan->bahan))
                                            @if ($lastMasakan != $m->rincianMenuKarbohidrat->first()->bahan->bahan)
                                                {{ $no }}
                                                @php
                                                    $lastMasakan = $m->rincianMenuKarbohidrat->first()->bahan->bahan;
                                                    $makananNow = $m->rincianMenuKarbohidrat->first()->bahan->bahan;
                                                    $no++;
                                                @endphp
                                            @else
                                                @php
                                                    $makananNow = '';
                                                @endphp
                                            @endif
                                        @endif
                                            
                                    </td>
                                    <td>
                                        @if ($lastMenu != $m->menu)
                                            {{ $m->menu }}
                                            @php
                                                $lastMenu = $m->menu;
                                            @endphp
                                            
                                        @endif
                                    </td>
                                    <td>
                                        {{ $makananNow }}
                                    </td>
                                    @if ($lastBahan != $bahan->bahan->bahan)

                                    <td>{{ $bahan->bahan->bahan }}</td>
                                    <td>{{ $bahan->jumlah }}</td>
                                    <td>{{ $transaksi->satuan}}</td>
                                        @php
                                            $lastBahan = $bahan->bahan->bahan;
                                        @endphp
                                    @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                    <td>
                                        {{ $transaksi->jumlah_masuk }}
                                    </td>
                                    <td>
                                        {{ $bahan->bahan->satuanBahan->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_masuk }}
                                    </td>
                                    <td></td>

                                    <td>
                                        {{ $transaksi->jumlah_keluar }}
                                    </td>
                                    <td>
                                        {{ $bahan->bahan->satuanBahan->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_keluar }}
                                    </td>
                                    <td></td>
                                </tr>

                                
                                
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @endif

            @if($m->rincianMenuProtein)
                @foreach ($m->rincianMenuProtein as $bahan)
                    @foreach ($bahan->tbPoBahan as $poBahan)  <!-- Loop untuk tbPoBahan -->
                        @foreach ($poBahan->tbPenerimaan as $penerimaan) <!-- Loop untuk tbPenerimaan -->
                            @foreach ($penerimaan->warehouseTransaksi as $index => $transaksi) <!-- Loop untuk warehouseTransaksi -->
                                
                                <tr>
                                    <td>
                                        @if (isset($m->rincianMenuProtein->first()->bahan->bahan))
                                            @if ($lastMasakan != $m->rincianMenuProtein->first()->bahan->bahan)
                                                {{ $no }}
                                                @php
                                                    $lastMasakan = $m->rincianMenuProtein->first()->bahan->bahan;
                                                    $makananNow = $m->rincianMenuProtein->first()->bahan->bahan;
                                                    $no++;
                                                @endphp
                                            @else
                                                @php
                                                    $makananNow = '';
                                                @endphp
                                            @endif
                                        @endif
                                            
                                    </td>
                                    <td>
                                        @if ($lastMenu != $m->menu)
                                            {{ $m->menu }}
                                            @php
                                                $lastMenu = $m->menu;
                                            @endphp
                                            
                                        @endif
                                    </td>
                                    <td>
                                        {{ $makananNow }}
                                    </td>
                                    @if ($lastBahan != $bahan->bahan->bahan)

                                    <td>{{ $bahan->bahan->bahan }}</td>
                                    <td>{{ $bahan->jumlah }}</td>
                                    <td>{{ $bahan->bahan->satuanBahan->satuan }}</td>
                                        @php
                                            $lastBahan = $bahan->bahan->bahan;
                                        @endphp
                                    @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                    <td>
                                        {{ $transaksi->jumlah_masuk }}
                                    </td>
                                    <td>
                                        {{ $transaksi->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_masuk }}
                                    </td>
                                    <td></td>

                                    <td>
                                        {{ $transaksi->jumlah_keluar }}
                                    </td>
                                    <td>
                                        {{ $transaksi->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_keluar }}
                                    </td>
                                    <td></td>
                                </tr>

                                
                                
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @endif


            @if($m->rincianMenuSayur)
                @foreach ($m->rincianMenuSayur as $bahan)
                    @foreach ($bahan->tbPoBahan as $poBahan)  <!-- Loop untuk tbPoBahan -->
                        @foreach ($poBahan->tbPenerimaan as $penerimaan) <!-- Loop untuk tbPenerimaan -->
                            @foreach ($penerimaan->warehouseTransaksi as $index => $transaksi) <!-- Loop untuk warehouseTransaksi -->
                                
                                <tr>
                                    <td>
                                        @if (isset($m->rincianMenuSayur->first()->bahan->bahan))
                                            @if ($lastMasakan != $m->rincianMenuSayur->first()->bahan->bahan)
                                                {{ $no }}
                                                @php
                                                    $lastMasakan = $m->rincianMenuSayur->first()->bahan->bahan;
                                                    $makananNow = $m->rincianMenuSayur->first()->bahan->bahan;
                                                    $no++;
                                                @endphp
                                            @else
                                                @php
                                                    $makananNow = '';
                                                @endphp
                                            @endif
                                        @endif
                                            
                                    </td>
                                    <td>
                                        @if ($lastMenu != $m->menu)
                                            {{ $m->menu }}
                                            @php
                                                $lastMenu = $m->menu;
                                            @endphp
                                            
                                        @endif
                                    </td>
                                    <td>
                                        {{ $makananNow }}
                                    </td>
                                    @if ($lastBahan != $bahan->bahan->bahan)

                                    <td>{{ $bahan->bahan->bahan }}</td>
                                    <td>{{ $bahan->jumlah }}</td>
                                    <td>{{ $transaksi->satuan }}</td>
                                        @php
                                            $lastBahan = $bahan->bahan->bahan;
                                        @endphp
                                    @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                    <td>
                                        {{ $transaksi->jumlah_masuk }}
                                    </td>
                                    <td>
                                        {{ $bahan->bahan->satuanBahan->satuan  }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_masuk }}
                                    </td>
                                    <td></td>

                                    <td>
                                        {{ $transaksi->jumlah_keluar }}
                                    </td>
                                    <td>
                                        {{ $bahan->bahan->satuanBahan->satuan  }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_keluar }}
                                    </td>
                                    <td></td>
                                </tr>

                                
                                
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @endif


            @if($m->rincianMenuBuah)
                @foreach ($m->rincianMenuBuah as $bahan)
                    @foreach ($bahan->tbPoBahan as $poBahan)  <!-- Loop untuk tbPoBahan -->
                        @foreach ($poBahan->tbPenerimaan as $penerimaan) <!-- Loop untuk tbPenerimaan -->
                            @foreach ($penerimaan->warehouseTransaksi as $index => $transaksi) <!-- Loop untuk warehouseTransaksi -->
                                
                            <tr>
                                    <td>
                                        @if (isset($m->rincianMenuBuah->first()->bahan->bahan))
                                            @if ($lastMasakan != $m->rincianMenuBuah->first()->bahan->bahan)
                                                {{ $no }}
                                                @php
                                                    $lastMasakan = $m->rincianMenuBuah->first()->bahan->bahan;
                                                    $makananNow = $m->rincianMenuBuah->first()->bahan->bahan;
                                                    $no++;
                                                @endphp
                                            @else
                                                @php
                                                    $makananNow = '';
                                                @endphp
                                            @endif
                                        @endif
                                            
                                    </td>
                                    <td>
                                        @if ($lastMenu != $m->menu)
                                            {{ $m->menu }}
                                            @php
                                                $lastMenu = $m->menu;
                                            @endphp
                                            
                                        @endif
                                    </td>
                                    <td>
                                        {{ $makananNow }}
                                    </td>
                                    @if ($lastBahan != $bahan->bahan->bahan)

                                    <td>{{ $bahan->bahan->bahan }}</td>
                                    <td>{{ $bahan->jumlah }}</td>
                                    <td>{{ $bahan->bahan->satuanBahan->satuan }}</td>
                                        @php
                                            $lastBahan = $bahan->bahan->bahan;
                                        @endphp
                                    @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                    <td>
                                        {{ $transaksi->jumlah_masuk }}
                                    </td>
                                    <td>
                                        {{ $transaksi->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_masuk }}
                                    </td>
                                    <td></td>

                                    <td>
                                        {{ $transaksi->jumlah_keluar }}
                                    </td>
                                    <td>
                                        {{ $transaksi->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_keluar }}
                                    </td>
                                    <td></td>
                                </tr>

                                
                                
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @endif

            @if($m->rincianMenuSusu)
                @foreach ($m->rincianMenuSusu as $bahan)
                    @foreach ($bahan->tbPoBahan as $poBahan)  <!-- Loop untuk tbPoBahan -->
                        @foreach ($poBahan->tbPenerimaan as $penerimaan) <!-- Loop untuk tbPenerimaan -->
                            @foreach ($penerimaan->warehouseTransaksi as $index => $transaksi) <!-- Loop untuk warehouseTransaksi -->
                                
                                <tr>
                                    <td>
                                        @if (isset($m->rincianMenuSusu->first()->bahan->bahan))
                                            @if ($lastMasakan != $m->rincianMenuSusu->first()->bahan->bahan)
                                                {{ $no }}
                                                @php
                                                    $no++;
                                                @endphp
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lastMenu != $m->menu)
                                            {{ $m->menu }}
                                            @php
                                                $lastMenu = $m->menu;
                                            @endphp
                                            
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($m->rincianMenuSusu->first()->bahan->bahan))
                                            @if ($lastMasakan != $m->rincianMenuSusu->first()->bahan->bahan)
                                                {{ $m->rincianMenuSusu->first()->bahan->bahan }}
                                                @php
                                                    $lastMasakan = $m->rincianMenuSusu->first()->bahan->bahan;
                                                    $no++;
                                                @endphp
                                            @endif
                                        @endif
                                    </td>
                                    @if ($lastBahan != $bahan->bahan->bahan)

                                    <td>{{ $bahan->bahan->bahan }}</td>
                                    <td>{{ $bahan->jumlah }}</td>
                                    <td>{{ $bahan->bahan->satuanBahan->satuan }}</td>
                                        @php
                                            $lastBahan = $bahan->bahan->bahan;
                                        @endphp
                                    @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    @endif
                                    <td>
                                        {{ $transaksi->jumlah_masuk }}
                                    </td>
                                    <td>
                                        {{ $transaksi->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_masuk }}
                                    </td>
                                    <td></td>

                                    <td>
                                        {{ $transaksi->jumlah_keluar }}
                                    </td>
                                    <td>
                                        {{ $transaksi->satuan }}
                                    </td>
                                    <td>
                                        {{ $transaksi->box_keluar }}
                                    </td>
                                    <td></td>
                                </tr>

                                
                                
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @endif

        @endforeach

            

            
            
    </table>
@else
    <p>Menu tidak ditemukan</p>
@endif
