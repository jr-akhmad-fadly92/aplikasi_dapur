<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * UploadData Model
 * 
 * Represents uploaded documents/files untuk setiap menu
 * Ini adalah model untuk mengelola upload dokumen
 * 
 * @property int $id
 * @property int $id_menu - FK ke tb_menu
 * @property \Carbon\Carbon $tanggal_pelayanan - Tanggal pelayanan/delivery
 * @property string|null $data_menu - PDF file untuk menu
 * @property string|null $data_po - PDF file untuk Purchase Order (REQUIRED)
 * @property string|null $data_invoice - PDF file untuk Invoice (REQUIRED)
 * @property string|null $data_penerimaan_pangan - PDF untuk Penerimaan Pangan (REQUIRED)
 * @property string|null $data_hasil_masak - PDF untuk Hasil Masak (REQUIRED)
 * @property string|null $data_sj_sekolah - PDF untuk Surat Jalan Sekolah (REQUIRED)
 * @property string|null $data_counter_ompreng - PDF untuk Counter Ompreng (REQUIRED)
 * @property string|null $data_sj_kp - PDF untuk SJ/KP (OPTIONAL)
 * @property string|null $data_penerimaan_non_pangan - PDF untuk Non-Pangan (OPTIONAL)
 * @property string|null $data_gudang - PDF untuk Gudang (OPTIONAL)
 */
class UploadData extends Model
{
    protected $table = 'tb_upload_data';

    protected $fillable = [
        'id_menu',
        'tanggal_pelayanan',
        'data_menu',
        'data_po',
        'data_sj_kp',
        'data_invoice',
        'data_penerimaan_pangan',
        'data_uji_organoleptik',
        'data_penerimaan_non_pangan',
        'data_gudang',
        'data_hasil_masak',
        'data_sj_sekolah',
        'data_counter_ompreng',
    ];

    protected $dates = ['tanggal_pelayanan'];

    /**
     * Relationship ke Menu
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id');
    }

    /**
     * Get all document field mappings
     * Returns array dengan field name => label
     * 
     * Required files:
     * - data_po: PO (Purchase Order)
     * - data_invoice: Invoice
     * - data_penerimaan_pangan: Penerimaan Pangan
     * - data_hasil_masak: Hasil Masak
     * - data_sj_sekolah: SJ Sekolah
     * - data_counter_ompreng: Counter Ompreng
     * 
     * Optional files:
     * - data_menu: Menu
     * - data_sj_kp: SJ/KP
     * - data_penerimaan_non_pangan: Penerimaan Non-Pangan
     * - data_gudang: Gudang
     * 
     * @return array
     */
    public static function getDocumentFields()
    {
        return [
            'data_menu' => 'Menu',
            'data_uji_organoleptik' => 'Uji Organoleptik',
            'data_po' => 'PO',
            'data_sj_kp' => 'SJ/KP',
            'data_invoice' => 'Invoice',
            'data_penerimaan_pangan' => 'Penerimaan Pangan',
            'data_penerimaan_non_pangan' => 'Penerimaan Non-Pangan',
            'data_gudang' => 'Gudang',
            'data_hasil_masak' => 'Hasil Masak',
            'data_sj_sekolah' => 'SJ Sekolah',
            'data_counter_ompreng' => 'Counter Ompreng',
        ];
    }

    /**
     * Get required document fields (7 files)
     * 
     * @return array
     */
    public static function getRequiredFields()
    {
        return [
            'data_po',
            'data_invoice',
            'data_penerimaan_pangan',
            'data_hasil_masak',
            'data_sj_sekolah',
            'data_counter_ompreng',
        ];
    }

    /**
     * Get optional document fields (3 files)
     * 
     * @return array
     */
    public static function getOptionalFields()
    {
        return [
            'data_menu',
            'data_sj_kp',
            'data_penerimaan_non_pangan',
            'data_gudang',
        ];
    }

    /**
     * Check apakah semua required files sudah diupload
     * 
     * @return bool
     */
    public function isComplete()
    {
        foreach (self::getRequiredFields() as $field) {
            if (!$this->{$field}) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get array dari files yang tersedia
     * Useful untuk download dropdown
     * 
     * @return array
     */
    public function getAvailableFiles()
    {
        $files = [];
        $fields = self::getDocumentFields();
        
        foreach ($fields as $fieldName => $label) {
            if ($this->{$fieldName}) {
                $files[$fieldName] = $label;
            }
        }
        
        return $files;
    }

    /**
     * Scope: only records with all required files present
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeComplete($query)
    {
        foreach (self::getRequiredFields() as $field) {
            $query->whereNotNull($field)->where($field, '!=', '');
        }
        return $query;
    }

    /**
     * Scope: filter by exact service date (tanggal_pelayanan)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\DateTimeInterface $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('tanggal_pelayanan', $date);
    }

    /**
     * Scope: filter by date range (tanggal_pelayanan)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|\DateTimeInterface $start
     * @param string|\DateTimeInterface $end
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_pelayanan', [$start, $end]);
    }

    /**
     * Scope: filter by menu id
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $menuId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMenu($query, $menuId)
    {
        return $query->where('id_menu', $menuId);
    }
}

