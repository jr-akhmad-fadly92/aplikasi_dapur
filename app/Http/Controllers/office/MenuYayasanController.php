<?php

namespace App\Http\Controllers\office;

use App\Models\Menu;
use App\Models\Resep;
use App\Models\rincian_sekolah;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class MenuYayasanController extends TbMasterMenuController
{
    public function index()
    {
        $header = 'Pengajuan Menu Yayasan';

        if (request()->ajax()) {
            $menu = Menu::select('tb_menu.*')
                ->where('tb_menu.status_pengajuan', '!=', 'rejected');

            if (request()->has('search_menu') && !empty(request()->get('search_menu'))) {
                $searchMenu = request()->get('search_menu');
                $menu = $menu->where(function ($q) use ($searchMenu) {
                    $q->where('tb_menu.menu', 'like', '%' . $searchMenu . '%')
                        ->orWhereIn('tb_menu.karbohidrat', function ($q2) use ($searchMenu) {
                            $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                        })
                        ->orWhereIn('tb_menu.protein', function ($q2) use ($searchMenu) {
                            $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                        })
                        ->orWhereIn('tb_menu.sayur', function ($q2) use ($searchMenu) {
                            $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                        })
                        ->orWhereIn('tb_menu.buah', function ($q2) use ($searchMenu) {
                            $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                        })
                        ->orWhereIn('tb_menu.susu', function ($q2) use ($searchMenu) {
                            $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                        });
                });
            }

            if (request()->has('tanggal_awal') && !empty(request()->get('tanggal_awal'))) {
                $menu = $menu->whereDate('tb_menu.tanggal_kirim', '>=', request()->get('tanggal_awal'));
            }

            if (request()->has('tanggal_akhir') && !empty(request()->get('tanggal_akhir'))) {
                $menu = $menu->whereDate('tb_menu.tanggal_kirim', '<=', request()->get('tanggal_akhir'));
            }

            $menu = $menu->latest()->limit(60)->get();

            return DataTables::of($menu)
                ->addIndexColumn()
                ->addColumn('nama_karbohidrat', function ($row) {
                    $resep = Resep::find($row->karbohidrat);
                    return $resep ? $resep->nama_resep : '--';
                })
                ->addColumn('nama_protein', function ($row) {
                    $resep = Resep::find($row->protein);
                    return $resep ? $resep->nama_resep : '--';
                })
                ->addColumn('nama_sayur', function ($row) {
                    $resep = Resep::find($row->sayur);
                    return $resep ? $resep->nama_resep : '--';
                })
                ->addColumn('nama_susu', function ($row) {
                    $resep = Resep::find($row->susu);
                    return $resep ? $resep->nama_resep : '--';
                })
                ->addColumn('nama_buah', function ($row) {
                    $resep = Resep::find($row->buah);
                    return $resep ? $resep->nama_resep : '--';
                })
                ->addColumn('jumlah_porsi', function ($row) {
                    $jumlah = rincian_sekolah::where('id_menu_harian', $row->id)->sum('jumlah_penerima_total');
                    return $jumlah . ' porsi';
                })
                ->addColumn('tanggal_masak', function ($row) {
                    return $row->menu . ' | ' . $row->status_pengajuan;
                })
                ->addColumn('action', function ($row) {
                    if (auth()->check() && in_array(auth()->user()->level, ['backoffice'])) {
                        return '<a href="' . route('menu_yayasan.edit', $row['id']) . '" class="edit btn btn-primary btn-sm" data-id="' . $row['id'] . '">Edit</a>
                                <a href="' . route('menu_yayasan.delete', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" data-id="' . $row['id'] . '">Delete</a>
                                <a href="' . route('rincian_bahan', $row['id']) . '" class="edit btn btn-success btn-sm" data-id="' . $row['id'] . '">Detail Bahan</a>
                                <a href="' . route('export.detail_pengajuan_menu.excel', $row->id) . '" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-file-excel"></i>excel</a>
                                <a href="' . route('pdf_pengajuan_menu_3', $row['id']) . '" class="edit btn btn-secondary btn-sm">Pengajuan</a>
                                <a href="' . route('cetak.rekap.ceklist', $row['id']) . '" class="edit btn btn-secondary btn-sm">Checklist masak</a>
                                <a href="' . route('cetak.rekap.ceklist_hasil_masak', $row['id']) . '" class="edit btn btn-secondary btn-sm">Checklist Hasil Masak</a>';
                    }

                    return '<a href="' . route('rincian_bahan', $row['id']) . '" class="edit btn btn-success btn-sm" data-id="' . $row['id'] . '">Detail Bahan</a>
                            <a href="' . route('export.detail_pengajuan_menu.excel', $row->id) . '" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-file-excel"></i>excel</a>
                            <a href="' . route('pdf_pengajuan_menu_3', $row['id']) . '" class="edit btn btn-secondary btn-sm">Pengajuan</a>
                            <button class="btn btn-success btn-sm acc-button" data-id="' . $row['id'] . '">ACC</button>
                            <a href="' . route('cetak.rekap.ceklist', $row['id']) . '" class="edit btn btn-secondary btn-sm">Checklist masak</a>
                            <a href="' . route('cetak.rekap.ceklist_hasil_masak', $row['id']) . '" class="edit btn btn-secondary btn-sm">Checklist Hasil Masak</a>';
                })
                ->rawColumns(['action', 'jumlah_porsi'])
                ->make(true);
        }

        return view('office.menu_yayasan.index', compact('header'));
    }
}
