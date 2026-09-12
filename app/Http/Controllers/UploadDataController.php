<?php

namespace App\Http\Controllers;

use App\Models\UploadData;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UploadDataController extends Controller
{
    protected $storagePath = 'data_dapur';
    protected $maxFileSize = 1048576; // 1MB in bytes

    public function index()
    {
        if (request()->wantsJson()) {
            return $this->getDataTable();
        }
        
        $menus = Menu::orderBy('menu')->get();
        return view('office.upload_data.index', compact('menus'));
    }

    public function getMenusByDate(Request $request)
    {
        try {
            $date = $request->query('date');
            
            if (!$date) {
                return response()->json(['data' => []]);
            }

            $menus = Menu::where('tanggal_kirim', $date)
                ->orderBy('menu')
                ->get(['id', 'menu']);

            return response()->json(['data' => $menus]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()], 500);
        }
    }

    protected function getDataTable()
    {
        $uploads = UploadData::with('menu')->orderBy('id', 'desc')->get();
        
        $data = [];
        foreach ($uploads as $upload) {
            $documents = [];
            foreach (UploadData::getDocumentFields() as $field => $label) {
                $documents[] = [
                    'field' => $field,
                    'label' => $label,
                    'file' => $upload->$field
                ];
            }

            $data[] = [
                'id' => $upload->id,
                'tanggal_pelayanan' => $upload->tanggal_pelayanan ? Carbon::parse($upload->tanggal_pelayanan)->format('d/m/Y') : '-',
                'menu' => $upload->menu ? $upload->menu->menu : '-',
                'documents' => $documents,
                'actions' => view('office.upload_data.partials.actions', compact('upload'))->render()
            ];
        }
        
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_menu' => 'nullable|exists:tb_menu,id',
                'tanggal_pelayanan' => 'required|date',
                'data_uji_organoleptik' => 'nullable|file|mimes:pdf|max:10240',
                'data_menu' => 'nullable|file|mimes:pdf|max:10240',
                'data_po' => 'nullable|file|mimes:pdf|max:10240',
                'data_sj_kp' => 'nullable|file|mimes:pdf|max:10240',
                'data_invoice' => 'nullable|file|mimes:pdf|max:10240',
                'data_penerimaan_pangan' => 'nullable|file|mimes:pdf|max:10240',
                'data_penerimaan_non_pangan' => 'nullable|file|mimes:pdf|max:10240',
                'data_gudang' => 'nullable|file|mimes:pdf|max:10240',
                'data_hasil_masak' => 'nullable|file|mimes:pdf|max:10240',
                'data_sj_sekolah' => 'nullable|file|mimes:pdf|max:10240',
                'data_counter_ompreng' => 'nullable|file|mimes:pdf|max:10240',
            ], [
                '*.mimes' => 'File harus berformat PDF',
                '*.max' => 'Ukuran file maksimal 10MB',
            ]);

            $uploadData = UploadData::create([
                'id_menu' => $validated['id_menu'],
                'tanggal_pelayanan' => $validated['tanggal_pelayanan'],
            ]);
            
            // Process each document field
            foreach (UploadData::getDocumentFields() as $field => $label) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = $this->storeFile($file, $field, $validated['tanggal_pelayanan'], $uploadData->id_menu);
                    if ($fileName) {
                        $uploadData->update([$field => $fileName]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $uploadData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        $upload = UploadData::findOrFail($id);
        $menus = Menu::orderBy('menu')->get();
        $documents = UploadData::getDocumentFields();

        return view('office.upload_data.partials.edit-modal', compact('upload', 'menus', 'documents'));
    }

    public function update(Request $request, $id)
    {
        try {
            $upload = UploadData::findOrFail($id);
            
            $validated = $request->validate([
                'id_menu' => 'nullable|exists:tb_menu,id',
                'tanggal_pelayanan' => 'required|date',
            ]);

            $upload->update($validated);

            // Process each document field for update
            foreach (UploadData::getDocumentFields() as $field => $label) {
                if ($request->hasFile($field)) {
                    // Delete old file if exists
                    if ($upload->$field && File::exists(public_path($this->storagePath . '/' . $upload->$field))) {
                        File::delete(public_path($this->storagePath . '/' . $upload->$field));
                    }
                    
                    $file = $request->file($field);
                    $fileName = $this->storeFile($file, $field, $validated['tanggal_pelayanan'], $upload->id_menu);
                    if ($fileName) {
                        $upload->update([$field => $fileName]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $upload = UploadData::findOrFail($id);
            
            // Delete all associated files
            foreach (UploadData::getDocumentFields() as $field => $label) {
                if ($upload->$field) {
                    $filePath = public_path($this->storagePath . '/' . $upload->$field);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }

            $upload->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 422);
        }
    }

    public function download($id, $field)
    {
        try {
            $upload = UploadData::findOrFail($id);
            
            if (!isset(UploadData::getDocumentFields()[$field])) {
                return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
            }

            $fileName = $upload->$field;
            if (!$fileName) {
                return response()->json(['message' => 'File tidak tersedia'], 404);
            }

            $filePath = public_path($this->storagePath . '/' . $fileName);
            
            if (!File::exists($filePath)) {
                return response()->json(['message' => 'File tidak ditemukan di server'], 404);
            }

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    protected function storeFile($file, $fieldName, $tanggalPelayanan, $idMenu)
    {
        try {
            // Ensure directory exists
            if (!File::isDirectory(public_path($this->storagePath))) {
                File::makeDirectory(public_path($this->storagePath), 0755, true);
            }

            // Generate filename: doctype_id_menu_tanggal.pdf
            $docType = str_replace('data_', '', $fieldName);
            $date = date('Ymd', strtotime($tanggalPelayanan));
            $menuId = $idMenu ?? '0';
            $fileName = $docType . '_' . $menuId . '_' . $date . '.pdf';

            // Get file content
            $fileContent = file_get_contents($file->getRealPath());
            $fileSize = strlen($fileContent);

            // Compress if needed
            if ($fileSize > $this->maxFileSize) {
                $fileContent = $this->compressPDF($file->getRealPath());
            }

            // Store file
            $storagePath = public_path($this->storagePath . '/' . $fileName);
            file_put_contents($storagePath, $fileContent);

            return $fileName;
        } catch (\Exception $e) {
            \Log::error('File storage error: ' . $e->getMessage());
            return null;
        }
    }

    protected function compressPDF($filePath)
    {
        try {
            // Try using ghostscript if available
            $output = tempnam(sys_get_temp_dir(), 'pdf_');
            $command = sprintf(
                'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s 2>/dev/null',
                escapeshellarg($output),
                escapeshellarg($filePath)
            );
            
            @exec($command);
            
            if (file_exists($output) && filesize($output) > 0) {
                $compressed = file_get_contents($output);
                @unlink($output);
                return $compressed;
            }
        } catch (\Exception $e) {
            \Log::warning('PDF compression failed, using original file: ' . $e->getMessage());
        }

        // Fallback: return original file if compression fails
        return file_get_contents($filePath);
    }
}
