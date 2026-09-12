<?php

namespace App\Services;

use App\Models\UploadData;
use App\Models\Menu;
use App\Exceptions\DapurExceptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * UploadDataService
 * Handles all business logic untuk upload data
 * Separates concerns dari controller
 */
class UploadDataService
{
    const UPLOAD_PATH = 'data_dapur';
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    const COMPRESSED_SIZE_TARGET = 1024 * 1024; // 1MB target
    
    /**
     * Store new upload data dengan files
     * 
     * @param array $data
     * @param array $files
     * @return UploadData
     * @throws DocumentValidationException
     */
    public function store(array $data, array $files): UploadData
    {
        try {
            $uploadData = new UploadData($data);
            
            // Process each file
            foreach ($files as $fieldName => $file) {
                if ($file) {
                    $storedPath = $this->storeFile($file, $data['id_menu'] ?? null, $fieldName);
                    $uploadData->{$fieldName} = $storedPath;
                }
            }
            
            $uploadData->save();
            
            Log::info('Upload data created', [
                'id' => $uploadData->id,
                'user_id' => auth()->id(),
                'files_count' => collect($files)->filter()->count()
            ]);
            
            return $uploadData;
            
        } catch (\Exception $e) {
            Log::error('Failed to store upload data: ' . $e->getMessage());
            throw new DapurExceptions\DocumentValidationException('Gagal menyimpan data upload: ' . $e->getMessage());
        }
    }

    /**
     * Update existing upload data
     * 
     * @param UploadData $uploadData
     * @param array $data
     * @param array $files
     * @return UploadData
     */
    public function update(UploadData $uploadData, array $data, array $files): UploadData
    {
        try {
            $uploadData->fill($data);
            
            // Process each new file (replace old one if exists)
            foreach ($files as $fieldName => $file) {
                if ($file) {
                    // Delete old file
                    if ($uploadData->{$fieldName}) {
                        Storage::disk('public')->delete($uploadData->{$fieldName});
                    }
                    
                    $storedPath = $this->storeFile($file, $data['id_menu'] ?? null, $fieldName);
                    $uploadData->{$fieldName} = $storedPath;
                }
            }
            
            $uploadData->save();
            
            Log::info('Upload data updated', [
                'id' => $uploadData->id,
                'user_id' => auth()->id()
            ]);
            
            return $uploadData;
            
        } catch (\Exception $e) {
            Log::error('Failed to update upload data: ' . $e->getMessage());
            throw new DapurExceptions\DocumentValidationException('Gagal mengupdate data upload');
        }
    }

    /**
     * Store individual file dengan compression jika diperlukan
     * 
     * @param $file
     * @param int|null $idMenu
     * @param string $fieldName
     * @return string
     */
    public function storeFile($file, ?int $idMenu, string $fieldName): string
    {
        // Validate file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new DapurExceptions\FileUploadException("File {$fieldName} terlalu besar (max 10MB)");
        }

        // Validate MIME type
        if ($file->getMimeType() !== 'application/pdf') {
            throw new DapurExceptions\FileUploadException("File {$fieldName} harus format PDF");
        }

        // Generate filename: {doctype}_{id_menu}_{tanggal}.pdf
        $docType = str_replace('_file', '', $fieldName);
        $tanggal = now()->format('Ymd');
        $suffix = Str::uuid()->toString();
        $filename = "{$docType}_{$idMenu}_{$tanggal}_{$suffix}.pdf";
        
        // Store file
        $storedPath = $file->storeAs(self::UPLOAD_PATH, $filename, 'public');
        
        // Compress if needed
        if ($file->getSize() > self::COMPRESSED_SIZE_TARGET) {
            $this->compressFile($storedPath);
        }
        
        return $storedPath;
    }

    /**
     * Compress PDF file menggunakan Ghostscript
     * 
     * @param string $filePath
     * @return void
     */
    private function compressFile(string $filePath): void
    {
        try {
            $publicPath = storage_path('app/public/');
            $fullPath = $publicPath . $filePath;
            
            if (!file_exists($fullPath)) {
                return;
            }

            if (!$this->isGhostscriptAvailable()) {
                Log::warning('PDF compression skipped: Ghostscript not available');
                return;
            }
            
            $tempPath = $fullPath . '.tmp.pdf';
            
            // Ghostscript compression command
            $command = sprintf(
                'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook ' .
                '-dNOPAUSE -dQUIET -dBATCH -dDetectDuplicateImages -r150x150 ' .
                '-sOutputFile=%s %s',
                escapeshellarg($tempPath),
                escapeshellarg($fullPath)
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($tempPath)) {
                // Replace original with compressed
                unlink($fullPath);
                rename($tempPath, $fullPath);
                
                Log::info('PDF compressed', ['file' => $filePath]);
            }
        } catch (\Exception $e) {
            Log::warning('PDF compression failed: ' . $e->getMessage());
            // Don't throw - let it continue with uncompressed file
        }
    }

    /**
     * Check Ghostscript availability on host OS
     */
    private function isGhostscriptAvailable(): bool
    {
        try {
            $command = strtoupper(PHP_OS_FAMILY) === 'WINDOWS' ? 'where gs' : 'command -v gs';
            $output = [];
            $returnCode = 1;
            @exec($command, $output, $returnCode);
            return $returnCode === 0 && !empty($output);
        } catch (\Throwable $e) {
            Log::warning('Ghostscript availability check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete upload data dan semua files-nya
     * 
     * @param UploadData $uploadData
     * @return bool
     */
    public function delete(UploadData $uploadData): bool
    {
        try {
            // Delete all associated files
            $fields = UploadData::getDocumentFields();
            foreach (array_keys($fields) as $fieldName) {
                if ($uploadData->{$fieldName}) {
                    Storage::disk('public')->delete($uploadData->{$fieldName});
                }
            }
            
            // Delete record
            $uploadData->delete();
            
            Log::info('Upload data deleted', ['id' => $uploadData->id]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to delete upload data: ' . $e->getMessage());
            throw new DapurExceptions\DocumentValidationException('Gagal menghapus data upload');
        }
    }

    /**
     * Download file
     * 
     * @param UploadData $uploadData
     * @param string $field
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(UploadData $uploadData, string $field)
    {
        $filePath = $uploadData->{$field};
        
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            throw new DapurExceptions\FileUploadException("File tidak ditemukan");
        }
        
        return Storage::disk('public')->download($filePath);
    }

    /**
     * Get menus for a specific date
     * 
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMenusByDate(string $date)
    {
        return Menu::whereDate('tanggal_kirim', $date)
            ->select('id', 'menu', 'tanggal_kirim')
            ->orderBy('id')
            ->get();
    }

    /**
     * Validate menu exists and belongs to correct date
     * 
     * @param int $menuId
     * @param string $date
     * @return Menu
     * @throws InvalidMenuException
     */
    public function validateMenu(int $menuId, string $date): Menu
    {
        $menu = Menu::whereDate('tanggal_kirim', $date)
            ->find($menuId);
        
        if (!$menu) {
            throw new DapurExceptions\InvalidMenuException("Menu tidak valid untuk tanggal tersebut");
        }
        
        return $menu;
    }
}
