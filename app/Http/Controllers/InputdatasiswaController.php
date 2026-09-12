<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SiswaSekolah;
use App\Models\DataSekolah;
use App\Imports\SiswaSekolahImport;
use App\Exports\SiswaSekolahTemplateExport;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InputdatasiswaController extends Controller
{

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        $header = "Input Data Siswa";
        $sekolah_id = $request->get('sekolah_id');
        $sekolah = null;
        if ($sekolah_id) {
            $sekolah = DataSekolah::find($sekolah_id);
        }
        $dataSekolah = DataSekolah::orderBy('nama_sekolah', 'asc')->get();
        return view('office.inputdatasiswa.index', compact('header', 'sekolah', 'dataSekolah'));
    }

    public function dt_dataSiswa(Request $request)
    {
        $query = DB::table('siswa_sekolah')
            ->leftJoin('tb_data_sekolah', 'siswa_sekolah.npsn', '=', 'tb_data_sekolah.npsn')
            ->select([
                'siswa_sekolah.id',
                'siswa_sekolah.nisn',
                'siswa_sekolah.nama',
                'siswa_sekolah.kelas',
                'siswa_sekolah.jenis_kelamin',
                'tb_data_sekolah.nama_sekolah',
                'siswa_sekolah.npsn'
            ]);

        // Filter berdasarkan sekolah_id - jika ada parameter sekolah_id, tampilkan hanya data untuk sekolah tersebut
        if ($request->has('sekolah_id') && !empty($request->sekolah_id)) {
            // Ambil NPSN dari sekolah berdasarkan ID
            $sekolah = DataSekolah::find($request->sekolah_id);
            if ($sekolah) {
                $query->where('siswa_sekolah.npsn', $sekolah->npsn);
            }
        }

        // Filter berdasarkan NISN
        if ($request->has('nisn') && !empty($request->nisn)) {
            $query->where('siswa_sekolah.nisn', 'like', '%' . $request->nisn . '%');
        }

        // Filter berdasarkan nama
        if ($request->has('nama') && !empty($request->nama)) {
            $query->where('siswa_sekolah.nama', 'like', '%' . $request->nama . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('jenis_kelamin_text', function ($row) {
                return $row->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary edit-siswa" data-id="' . $row->id . '">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-info detail-siswa" data-id="' . $row->id . '">
                        <i class="fa fa-eye"></i> Detail
                    </button>
                    <button class="btn btn-sm btn-danger delete-siswa" data-id="' . $row->id . '">
                        <i class="fa fa-trash"></i> Hapus
                    </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function form_dataSiswa(Request $request)
    {
        $siswa = null;
        $header = "Tambah Data Siswa";
        $selected_sekolah = null;

        // Jika ada parameter sekolah_id dari URL (mode direct)
        if ($request->has('sekolah_id') && !empty($request->sekolah_id)) {
            $selected_sekolah = DataSekolah::find($request->sekolah_id);
        }

        // Jika edit
        if ($request->has('id') && !empty($request->id)) {
            $siswa = SiswaSekolah::find($request->id);
            $header = "Edit Data Siswa";

            // Jika siswa ditemukan dan memiliki NPSN, cari sekolah yang sesuai
            if ($siswa && $siswa->npsn) {
                $sekolah_siswa = DataSekolah::where('npsn', $siswa->npsn)->first();
                $siswa->sekolah_id = $sekolah_siswa ? $sekolah_siswa->id : null;

                // Jika tidak ada selected_sekolah dari parameter, gunakan sekolah dari siswa
                if (!$selected_sekolah && $sekolah_siswa) {
                    $selected_sekolah = $sekolah_siswa;
                }
            }
        }

        // Ambil data sekolah untuk dropdown sekolah (hanya jika tidak ada selected_sekolah)
        $sekolah_list = $selected_sekolah ? collect() : DataSekolah::orderBy('nama_sekolah', 'asc')->get();

        return view('office.inputdatasiswa.form.formDataSiswa', compact('siswa', 'header', 'sekolah_list', 'selected_sekolah'));
    }

    private function validateSiswaData(Request $request, $siswaId = null)
    {
        // Buat rule unique yang lebih robust untuk NISN
        $nisnRule = 'required|string|max:20|unique:siswa_sekolah,nisn';
        if ($siswaId) {
            $nisnRule .= ',' . $siswaId . ',id';
        }

        $rules = [
            'nisn' => $nisnRule,
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:10',
            'jenis_kelamin' => 'required|in:L,P',
            'sekolah_id' => 'nullable|exists:tb_data_sekolah,id',
            'npsn' => 'nullable|string|max:20',
            'nama_orangtua' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            // Data Fisik & Kelahiran
            'berat_badan' => 'nullable|numeric|min:0|max:999.99',
            'tinggi_badan' => 'nullable|numeric|min:0|max:999.99',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            // Data Kesehatan
            'riwayat_penyakit_bawaan' => 'nullable|string',
            'riwayat_penyakit_menular' => 'nullable|string',
            'alergi' => 'nullable|string',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            // Data Kontak & Administrasi
            'nomor_telp_emergency' => 'nullable|string|max:15|regex:/^[0-9+\-\s\(\)]{10,15}$/',
            'golongan_penerimaan' => 'nullable|in:A,B'
        ];

        $messages = [
            'nisn.required' => 'NISN wajib diisi',
            'nisn.max' => 'NISN maksimal 20 karakter',
            'nisn.unique' => 'NISN sudah terdaftar, gunakan NISN yang lain',
            'nama.required' => 'Nama siswa wajib diisi',
            'nama.max' => 'Nama siswa maksimal 255 karakter',
            'kelas.required' => 'Kelas wajib diisi',
            'kelas.max' => 'Kelas maksimal 10 karakter',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'sekolah_id.exists' => 'Sekolah tidak valid',
            'npsn.max' => 'NPSN maksimal 20 karakter',
            'nama_orangtua.max' => 'Nama orangtua maksimal 255 karakter',
            // Validasi Data Fisik & Kelahiran
            'berat_badan.numeric' => 'Berat badan harus berupa angka',
            'berat_badan.min' => 'Berat badan tidak boleh kurang dari 0',
            'berat_badan.max' => 'Berat badan maksimal 999.99 kg',
            'tinggi_badan.numeric' => 'Tinggi badan harus berupa angka',
            'tinggi_badan.min' => 'Tinggi badan tidak boleh kurang dari 0',
            'tinggi_badan.max' => 'Tinggi badan maksimal 999.99 cm',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'tempat_lahir.max' => 'Tempat lahir maksimal 255 karakter',
            // Validasi Data Kesehatan
            'golongan_darah.in' => 'Golongan darah harus A, B, AB, atau O',
            // Validasi Data Kontak & Administrasi
            'nomor_telp_emergency.max' => 'Nomor telepon maksimal 15 karakter',
            'nomor_telp_emergency.regex' => 'Format nomor telepon tidak valid (10-15 karakter)',
            'golongan_penerimaan.in' => 'Golongan penerimaan harus A atau B'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    private function prepareSiswaData(Request $request)
    {
        $npsn = $request->npsn;

        // Prioritas: sekolah_id > npsn langsung
        if ($request->sekolah_id) {
            $sekolah = DataSekolah::find($request->sekolah_id);
            if ($sekolah) {
                $npsn = $sekolah->npsn;
            }
        }

        return [
            'npsn' => $npsn,
            'nisn' => $request->nisn,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nama_orangtua' => $request->nama_orangtua,
            'keterangan' => $request->keterangan,
            // Data Fisik & Kelahiran
            'berat_badan' => $request->berat_badan ? (float) $request->berat_badan : null,
            'tinggi_badan' => $request->tinggi_badan ? (float) $request->tinggi_badan : null,
            'tanggal_lahir' => $request->tanggal_lahir ?: null,
            'tempat_lahir' => $request->tempat_lahir,
            // Data Kesehatan
            'riwayat_penyakit_bawaan' => $request->riwayat_penyakit_bawaan,
            'riwayat_penyakit_menular' => $request->riwayat_penyakit_menular,
            'alergi' => $request->alergi,
            'golongan_darah' => $request->golongan_darah,
            // Data Kontak & Administrasi
            'nomor_telp_emergency' => $request->nomor_telp_emergency,
            'golongan_penerimaan' => $request->golongan_penerimaan
        ];
    }

    public function ajax_simpanSiswa(Request $request)
    {
        $isEdit = $request->has('id') && !empty($request->id);
        $siswaId = $isEdit ? $request->id : null;

        // Validasi input
        $validator = $this->validateSiswaData($request, $siswaId);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        try {
            $data = $this->prepareSiswaData($request);

            if ($isEdit) {
                $siswa = SiswaSekolah::find($siswaId);

                if (!$siswa) {
                    return $this->errorResponse('Data siswa tidak ditemukan', 404);
                }

                $siswa->update($data);
                $message = 'Data siswa berhasil diperbarui';
            } else {
                SiswaSekolah::create($data);
                $message = 'Data siswa berhasil ditambahkan';
            }

            return $this->successResponse($message);

        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function ajax_deleteSiswa(Request $request)
    {
        if (!$request->has('id')) {
            return $this->errorResponse('ID siswa tidak ditemukan');
        }

        try {
            $siswa = SiswaSekolah::find($request->id);

            if (!$siswa) {
                return $this->errorResponse('Data siswa tidak ditemukan', 404);
            }

            $siswa->delete();

            return $this->successResponse('Data siswa berhasil dihapus');

        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function getSekolahList(Request $request)
    {
        $query = DataSekolah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_sekolah', 'LIKE', "%{$search}%")
                    ->orWhere('npsn', 'LIKE', "%{$search}%");
            });
        }

        $sekolah = $query->select('id', 'nama_sekolah', 'npsn', 'jenjang_sekolah')
            ->orderBy('nama_sekolah', 'asc')
            ->limit(20)
            ->get();

        return $this->successResponse('Data sekolah berhasil diambil', $sekolah);
    }

    public function importSiswa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,xlsx,xls|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'File tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');

            // Buat instance import
            $import = new SiswaSekolahImport();

            // Import file
            Excel::import($import, $file);

            // Hitung hasil import
            $totalProcessed = $import->getTotalProcessed();
            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $errors = $import->getErrors();

            // Tambahkan errors dari validation failures
            $validationFailures = $import->failures();
            foreach ($validationFailures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                $failedCount++;
            }

            // Buat response dengan summary
            $message = "Import selesai. {$successCount} data berhasil";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} data gagal";
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'summary' => [
                    'total' => $totalProcessed,
                    'success' => $successCount,
                    'failed' => $failedCount,
                    'errors' => array_slice($errors, 0, 10) // Batasi error yang ditampilkan
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(
                new SiswaSekolahTemplateExport(),
                'template_import_siswa.xlsx'
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendownload template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportSiswa(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'format' => 'required|in:pdf',
                'sekolah_id' => 'required|exists:tb_data_sekolah,id',
                'kelas' => 'required|string',
                'columns' => 'required|array|min:1'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            // Ambil data sekolah
            $sekolah = DataSekolah::find($request->sekolah_id);
            if (!$sekolah) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }
            // Build query dengan filter
            $query = DB::table('siswa_sekolah')
                ->leftJoin('tb_data_sekolah', 'siswa_sekolah.npsn', '=', 'tb_data_sekolah.npsn')
                ->select([
                    'siswa_sekolah.id',
                    'siswa_sekolah.nisn',
                    'siswa_sekolah.nama',
                    'siswa_sekolah.kelas',
                    'siswa_sekolah.jenis_kelamin',
                    'tb_data_sekolah.nama_sekolah',
                    'siswa_sekolah.npsn'
                ])
                ->where('siswa_sekolah.npsn', $sekolah->npsn)
                ->where('siswa_sekolah.kelas', $request->kelas)
                ->orderBy('siswa_sekolah.nama', 'asc');
            $data = $query->get();
            if ($data->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data siswa untuk sekolah dan kelas yang dipilih'
                ], 404);
            }
            // Prepare data untuk export dengan format yang diinginkan
            $exportData = [
                'sekolah' => $sekolah,
                'kelas' => $request->kelas,
                'tanggal' => now(),
                'siswa_list' => $data
            ];
            // Generate filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "daftar_siswa_{$sekolah->npsn}_{$request->kelas}_{$timestamp}";
            // Export ke PDF format
            return $this->exportToPDF($exportData, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat export: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportToPDF($data, $filename)
    {
        $templateData = [
            'sekolah' => $data['sekolah'],
            'siswaData' => collect($data['siswa_list']),
            'kelas' => $data['kelas'] ?? null,
            'tanggal' => $data['tanggal'] ?? now()
        ];
        $html = view('office.inputdatasiswa.pdf.daftar_siswa', $templateData)->render();
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
        ]);
        return $pdf->download($filename . '.pdf');
    }

    private function exportToPDF_presensi($data, $filename)
    {
        $templateData = [
            'sekolah' => $data['sekolah'],
            'siswaData' => collect($data['siswa_list']),
            'kelas' => $data['kelas'] ?? null,
            'tanggal' => $data['tanggal'] ?? now()
        ];
        $html = view('office.inputdatasiswa.pdf.presensi_siswa', $templateData)->render();
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
        ]);
        return $pdf->download($filename . '.pdf');
    }


    public function form_import()
    {
        return view('office.inputdatasiswa.form.importSiswa');
    }

    public function form_export(Request $request)
    {
        $sekolah_id = $request->get('sekolah_id');
        $selected_sekolah = null;
        if ($sekolah_id) {
            $selected_sekolah = DataSekolah::find($sekolah_id);
        }
        $sekolah_list = DataSekolah::orderBy('nama_sekolah', 'asc')->get();
        return view('office.inputdatasiswa.form.export', compact('sekolah_list', 'selected_sekolah'));
    }

    public function previewExport(Request $request)
    {
        try {
            // Validasi input
            if (!$request->has('sekolah_id') || empty($request->sekolah_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sekolah harus dipilih'
                ], 400);
            }
            if (!$request->has('kelas') || empty($request->kelas)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kelas harus diisi'
                ], 400);
            }
            // Ambil data sekolah
            $sekolah = DataSekolah::find($request->sekolah_id);
            if (!$sekolah) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }
            $query = SiswaSekolah::where('npsn', $sekolah->npsn)
                ->where('kelas', $request->kelas);
            $count = $query->count();
            return response()->json([
                'status' => 'success',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal preview data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewExport_presensi(Request $request)
    {
        try {
            // Validasi input
            if (!$request->has('sekolah_id') || empty($request->sekolah_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sekolah harus dipilih'
                ], 400);
            }
            if (!$request->has('kelas') || empty($request->kelas)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kelas harus diisi'
                ], 400);
            }
            // Ambil data sekolah
            $sekolah = DataSekolah::find($request->sekolah_id);
            if (!$sekolah) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }
            $query = SiswaSekolah::where('npsn', $sekolah->npsn)
                ->where('kelas', $request->kelas);
            $count = $query->count();
            return response()->json([
                'status' => 'success',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal preview data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function errorResponse($message, $code = 400, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        if ($errors) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $code);
    }

    public function detail_siswa(Request $request)
    {
        try {
            $siswa = SiswaSekolah::with('sekolah')->find($request->id);

            if (!$siswa) {
                return $this->errorResponse('Data siswa tidak ditemukan', 404);
            }

            return view('office.inputdatasiswa.detailSiswa', compact('siswa'));
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    private function successResponse($message, $data = null)
    {
        $response = [
            'status' => 'success',
            'message' => $message
        ];
        if ($data) {
            $response['data'] = $data;
        }
        return response()->json($response);
    }

    public function form_export_presensi(Request $request)
    {
        $sekolah_id = $request->get('sekolah_id');
        $selected_sekolah = null;
        if ($sekolah_id) {
            $selected_sekolah = DataSekolah::find($sekolah_id);
        }
        $sekolah_list = DataSekolah::orderBy('nama_sekolah', 'asc')->get();
        return view('office.inputdatasiswa.form.export_presensi', compact('sekolah_list', 'selected_sekolah'));
    }

    public function exportSiswa_Presensi(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'format' => 'required|in:pdf',
                'sekolah_id' => 'required|exists:tb_data_sekolah,id',
                'kelas' => 'required|string',
                'columns' => 'required|array|min:1'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            // Ambil data sekolah
            $sekolah = DataSekolah::find($request->sekolah_id);
            if (!$sekolah) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sekolah tidak ditemukan'
                ], 404);
            }
            // Build query dengan filter
            $query = DB::table('siswa_sekolah')
                ->leftJoin('tb_data_sekolah', 'siswa_sekolah.npsn', '=', 'tb_data_sekolah.npsn')
                ->select([
                    'siswa_sekolah.id',
                    'siswa_sekolah.nisn',
                    'siswa_sekolah.nama',
                    'siswa_sekolah.kelas',
                    'siswa_sekolah.jenis_kelamin',
                    'tb_data_sekolah.nama_sekolah',
                    'siswa_sekolah.npsn'
                ])
                ->where('siswa_sekolah.npsn', $sekolah->npsn)
                ->where('siswa_sekolah.kelas', $request->kelas)
                ->orderBy('siswa_sekolah.nama', 'asc');
            $data = $query->get();
            if ($data->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data siswa untuk sekolah dan kelas yang dipilih'
                ], 404);
            }
            // Prepare data untuk export dengan format yang diinginkan
            $exportData = [
                'sekolah' => $sekolah,
                'kelas' => $request->kelas,
                'tanggal' => now(),
                'siswa_list' => $data
            ];
            // Generate filename
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "Presensi_siswa_{$sekolah->npsn}_{$request->kelas}_{$timestamp}";
            // Export ke PDF format
            return $this->exportToPDF_presensi($exportData, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat export: ' . $e->getMessage()
            ], 500);
        }
    }
}
