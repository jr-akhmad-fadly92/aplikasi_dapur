<?php

namespace App\Imports;

use App\Models\SiswaSekolah;
use App\Models\DataSekolah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Carbon\Carbon;

class SiswaSekolahImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $successCount = 0;
    private $failedCount = 0;
    private $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Convert numeric values to strings and clean data
                $npsn = (string) trim($row['npsn']);
                $nisn = (string) trim($row['nisn']);
                $nama = trim($row['nama']);
                $kelas = trim($row['kelas']);
                $jenisKelamin = strtoupper(trim($row['jenis_kelamin']));

                // Validasi NPSN exists di database
                // Sementara dinonaktifkan karena tabel data_sekolah belum ada
                /*
                $sekolah = DataSekolah::where('npsn', $npsn)->first();
                if (!$sekolah) {
                    $this->failedCount++;
                    $this->errors[] = "NPSN {$npsn} tidak ditemukan di database sekolah";
                    continue;
                }
                */

                // Cek apakah NISN sudah ada
                $existingSiswa = SiswaSekolah::where('nisn', $nisn)
                    ->where('npsn', $npsn)
                    ->first();

                if ($existingSiswa) {
                    // Update data yang sudah ada
                    $existingSiswa->update([
                        'nama' => $nama,
                        'kelas' => $kelas,
                        'jenis_kelamin' => $jenisKelamin,
                        'nama_orangtua' => isset($row['nama_orangtua']) ? trim($row['nama_orangtua']) : null,
                        'keterangan' => isset($row['keterangan']) ? trim($row['keterangan']) : null,
                        'berat_badan' => isset($row['berat_badan']) && $row['berat_badan'] !== '' ? (float) $row['berat_badan'] : null,
                        'tinggi_badan' => isset($row['tinggi_badan']) && $row['tinggi_badan'] !== '' ? (float) $row['tinggi_badan'] : null,
                        'tanggal_lahir' => isset($row['tanggal_lahir']) && $row['tanggal_lahir'] !== '' ? $this->parseDate($row['tanggal_lahir']) : null,
                        'tempat_lahir' => isset($row['tempat_lahir']) ? trim($row['tempat_lahir']) : null,
                        'riwayat_penyakit_bawaan' => isset($row['riwayat_penyakit_bawaan']) ? trim($row['riwayat_penyakit_bawaan']) : null,
                        'riwayat_penyakit_menular' => isset($row['riwayat_penyakit_menular']) ? trim($row['riwayat_penyakit_menular']) : null,
                        'alergi' => isset($row['alergi']) ? trim($row['alergi']) : null,
                        'nomor_telp_emergency' => isset($row['nomor_telp_emergency']) ? trim($row['nomor_telp_emergency']) : null,
                        'golongan_darah' => isset($row['golongan_darah']) ? strtoupper(trim($row['golongan_darah'])) : null,
                        'golongan_penerimaan' => isset($row['golongan_penerimaan']) ? strtoupper(trim($row['golongan_penerimaan'])) : null,
                        'alamat' => isset($row['alamat']) ? strtoupper(trim($row['alamat'])) : null,
                        'pekerjaan_orang_tua' => isset($row['pekerjaan_orang_tua']) ? strtoupper(trim($row['pekerjaan_orang_tua'])) : null,
                    ]);
                } else {
                    // Buat data baru
                    SiswaSekolah::create([
                        'npsn' => $npsn,
                        'nisn' => $nisn,
                        'nama' => $nama,
                        'kelas' => $kelas,
                        'jenis_kelamin' => $jenisKelamin,
                        'nama_orangtua' => isset($row['nama_orangtua']) ? trim($row['nama_orangtua']) : null,
                        'keterangan' => isset($row['keterangan']) ? trim($row['keterangan']) : null,
                        'berat_badan' => isset($row['berat_badan']) && $row['berat_badan'] !== '' ? (float) $row['berat_badan'] : null,
                        'tinggi_badan' => isset($row['tinggi_badan']) && $row['tinggi_badan'] !== '' ? (float) $row['tinggi_badan'] : null,
                        'tanggal_lahir' => isset($row['tanggal_lahir']) && $row['tanggal_lahir'] !== '' ? $this->parseDate($row['tanggal_lahir']) : null,
                        'tempat_lahir' => isset($row['tempat_lahir']) ? trim($row['tempat_lahir']) : null,
                        'riwayat_penyakit_bawaan' => isset($row['riwayat_penyakit_bawaan']) ? trim($row['riwayat_penyakit_bawaan']) : null,
                        'riwayat_penyakit_menular' => isset($row['riwayat_penyakit_menular']) ? trim($row['riwayat_penyakit_menular']) : null,
                        'alergi' => isset($row['alergi']) ? trim($row['alergi']) : null,
                        'nomor_telp_emergency' => isset($row['nomor_telp_emergency']) ? trim($row['nomor_telp_emergency']) : null,
                        'golongan_darah' => isset($row['golongan_darah']) ? strtoupper(trim($row['golongan_darah'])) : null,
                        'golongan_penerimaan' => isset($row['golongan_penerimaan']) ? strtoupper(trim($row['golongan_penerimaan'])) : null,
                        'alamat' => isset($row['alamat']) ? strtoupper(trim($row['alamat'])) : null,
                        'pekerjaan_orang_tua' => isset($row['pekerjaan_orang_tua']) ? strtoupper(trim($row['pekerjaan_orang_tua'])) : null,
                    ]);
                }

                $this->successCount++;
            } catch (\Exception $e) {
                $this->failedCount++;
                $nisn = isset($row['nisn']) ? $row['nisn'] : 'unknown';
                $this->errors[] = "Error pada baris NISN {$nisn}: " . $e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [
            'npsn' => 'required', // Bisa string atau numeric
            'nisn' => 'required', // Bisa string atau numeric  
            'nama' => 'required|string',
            'kelas' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P,l,p',
            'berat_badan' => 'nullable|numeric|min:0|max:999.99',
            'tinggi_badan' => 'nullable|numeric|min:0|max:999.99',
            'golongan_darah' => 'nullable|in:A,B,AB,O,a,b,ab,o',
            'golongan_penerimaan' => 'nullable|in:A,B,a,b',
            'nomor_telp_emergency' => 'nullable|max:12',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'npsn.required' => 'NPSN wajib diisi',
            'nisn.required' => 'NISN wajib diisi',
            'nama.required' => 'Nama siswa wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan)',
            'berat_badan.numeric' => 'Berat badan harus berupa angka',
            'berat_badan.min' => 'Berat badan tidak boleh negatif',
            'berat_badan.max' => 'Berat badan terlalu besar',
            'tinggi_badan.numeric' => 'Tinggi badan harus berupa angka',
            'tinggi_badan.min' => 'Tinggi badan tidak boleh negatif',
            'tinggi_badan.max' => 'Tinggi badan terlalu besar',
            'golongan_darah.in' => 'Golongan darah harus A, B, AB, atau O',
            'golongan_penerimaan.in' => 'Golongan penerimaan harus A atau B',
            'nomor_telp_emergency.max' => 'Nomor telepon emergency maksimal 12 karakter',
        ];
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailedCount()
    {
        return $this->failedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getTotalProcessed()
    {
        return $this->successCount + $this->failedCount;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle Excel date serial number
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }

            // Handle string dates
            $carbonDate = \Carbon\Carbon::parse($date);
            return $carbonDate->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
