<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

use App\Models\HasilMasak;
use App\Models\HistoriMenu;
use App\Models\Menu;
use App\Models\rincian_sekolah;
use App\Models\tbOmprengTransaksi;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;

class exportExcelRekapHasilMasak implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    protected $id;
    public function __construct(String $id)
    {
        $this->id = $id;
    }
    
    public function view(): View
    {

        App::setLocale('id'); // Pastikan lokal tersedia di server
        $datamenu = DB::table('tb_menu as m')
            ->leftJoin('tb_resep as r1', 'm.karbohidrat', '=', 'r1.id')
            ->leftJoin('tb_resep as r2', 'm.protein', '=', 'r2.id')
            ->leftJoin('tb_resep as r3', 'm.sayur', '=', 'r3.id')
            ->leftJoin('tb_resep as r4', 'm.buah', '=', 'r4.id')
            ->leftJoin('tb_resep as r5', 'm.susu', '=', 'r5.id')
            ->where('m.id', $this->id)
            ->select(
                'm.id',
                'r1.nama_resep as nama_karbohidrat',
                'r2.nama_resep as nama_protein',
                'r3.nama_resep as nama_sayur',
                'r4.nama_resep as nama_buah',
                'r5.nama_resep as nama_susu',
                'm.tanggal_kirim'
            )
            ->first();
        $jumlah_hasil_karbo         = HistoriMenu::where('id_menu', $this->id)->sum('hasil_menu_karbohidrat');
        $jumlah_hasil_protein       = HistoriMenu::where('id_menu', $this->id)->sum('hasil_menu_protein');
        $jumlah_hasil_sayur         = HistoriMenu::where('id_menu', $this->id)->sum('hasil_menu_sayur');
        $jumlah_hasil_buah          = HistoriMenu::where('id_menu', $this->id)->sum('hasil_menu_buah');
        $jumlah_hasil_susu          = HistoriMenu::where('id_menu', $this->id)->sum('hasil_menu_susu');

        $waktu = HistoriMenu::where('id_menu', $this->id)->first();
        $waktuMulai = Carbon::parse($waktu->waktu_mulai_masak);

        $hasil_pack_karbo           = $jumlah_hasil_karbo / 180;
        $satuan_pack_karbo          = "gram";
        $hasil_pack_protein         = $jumlah_hasil_protein / 100;
        $satuan_pack_protein        = "gram";
        $hasil_pack_sayur           = $jumlah_hasil_sayur / 100;
        $satuan_pack_sayur          = "gram";
        $hasil_pack_buah            = $jumlah_hasil_buah / 1;
        $satuan_pack_buah           = "biji";
        if ($datamenu->nama_susu == "tempe ***") {
            $hasil_pack_susu = $jumlah_hasil_susu / 50;
            $satuan_pack_susu          = "gram";
        } else {
            $hasil_pack_susu = $jumlah_hasil_susu / 1;
            $satuan_pack_susu          = "pcs";
        }

        $data_gastronom = DB::table('tb_hasil_masak')
            ->selectRaw('
                COUNT(CASE WHEN id_komponen_sehat = 1 THEN 1 END) AS jumlah_gastronom_karbo,
                COUNT(CASE WHEN id_komponen_sehat = 2 THEN 1 END) AS jumlah_gastronom_protein,
                COUNT(CASE WHEN id_komponen_sehat = 3 THEN 1 END) AS jumlah_gastronom_sayur,
                COUNT(CASE WHEN id_komponen_sehat = 4 THEN 1 END) AS jumlah_gastronom_buah,
                COUNT(CASE WHEN id_komponen_sehat = 5 THEN 1 END) AS jumlah_gastronom_susu
            ')
            ->where('id_menu', $this->id)
            ->first();

        

        $daftar_menu    = $datamenu->nama_karbohidrat.' , '. $datamenu->nama_sayur . ' , '. $datamenu->nama_protein . ' , '. $datamenu->nama_buah . ' , '. $datamenu->nama_susu ;
        $jumlah_porsi   = rincian_sekolah::where('id_menu_harian', $this->id)->sum('jumlah_penerima_total');
        $tanggal_menu   = Carbon::parse($datamenu->tanggal_kirim)->translatedFormat('l, j F Y');
        $jumlah_ompreng = tbOmprengTransaksi::where('tb_menu_id', $this->id)->count()?? 0;

        $hasilTerakhir = DB::table('tb_hasil_masak')
            ->select('id_komponen_sehat', DB::raw('MAX(waktu_matang) as waktu_terakhir'))
            ->where('id_menu', $this->id)
            ->whereIn('id_komponen_sehat', [1, 2, 3, 4, 5])
            ->groupBy('id_komponen_sehat')
            ->get();


        $waktukarbo  = $hasilTerakhir->firstWhere('id_komponen_sehat', 1)->waktu_terakhir ?? null;
        $waktuprotein = $hasilTerakhir->firstWhere('id_komponen_sehat', 2)->waktu_terakhir ?? null;
        $waktusayur = $hasilTerakhir->firstWhere('id_komponen_sehat', 3)->waktu_terakhir ?? null;
        $waktubuah = $hasilTerakhir->firstWhere('id_komponen_sehat', 4)->waktu_terakhir ?? null;
        $waktususu = $hasilTerakhir->firstWhere('id_komponen_sehat', 5)->waktu_terakhir ?? null;

        if ($waktukarbo) {
            $selisihMenit_karbo     = $waktuMulai->diffInMinutes($waktukarbo) ?? null;
        } else {
            $selisihMenit_karbo     = 0;
        }
        if ($waktuprotein) {
            $selisihMenit_protein     = $waktuMulai->diffInMinutes($waktuprotein) ?? null;
        } else {
            $selisihMenit_protein     = 0;
        }
        if ($waktuprotein) {
            $selisihMenit_protein     = $waktuMulai->diffInMinutes($waktuprotein) ?? null;
        } else {
            $selisihMenit_protein     = 0;
        }
        if ($waktusayur) {
            $selisihMenit_sayur     = $waktuMulai->diffInMinutes($waktusayur) ?? null;
        } else {
            $selisihMenit_sayur     = 0;
        }
        if ($waktubuah) {
            $selisihMenit_buah     = $waktuMulai->diffInMinutes($waktubuah) ?? null;
        } else {
            $selisihMenit_buah     = 0;
        }
        if ($waktususu) {
            $selisihMenit_susu     = $waktuMulai->diffInMinutes($waktususu) ?? null;
        } else {
            $selisihMenit_susu     = 0;
        }

        return view('exports.kebutuhan', compact(
            'datamenu',
            'jumlah_hasil_karbo',
            'jumlah_hasil_protein',
            'jumlah_hasil_sayur',
            'jumlah_hasil_buah',
            'jumlah_hasil_susu',
            'hasil_pack_karbo',
            'satuan_pack_karbo',
            'hasil_pack_protein',
            'satuan_pack_protein',
            'hasil_pack_sayur',
            'satuan_pack_sayur',
            'hasil_pack_buah',
            'satuan_pack_buah',
            'hasil_pack_susu',
            'satuan_pack_susu',
            'data_gastronom',
            'daftar_menu',
            'jumlah_porsi',
            'tanggal_menu',
            'jumlah_ompreng',
            'selisihMenit_susu',
            'selisihMenit_buah',
            'selisihMenit_sayur',
            'selisihMenit_protein',
            'selisihMenit_karbo',


        ));

        
    }
}
