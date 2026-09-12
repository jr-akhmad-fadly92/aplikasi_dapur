<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk upload data/dokumen
 * Handles validation untuk semua file uploads
 */
class StoreUploadDataRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $maxSize = '10240'; // 10MB dalam KB
        
        return [
            'tanggal_pelayanan' => ['required', 'date'],
            'id_menu' => ['nullable', 'exists:tb_menu,id'],
            
            // Required files (7)
            'menu_file' => ['required_without:id', 'nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'po_file' => ['required', 'file', 'mimes:pdf', "max:$maxSize"],
            'invoice_file' => ['required', 'file', 'mimes:pdf', "max:$maxSize"],
            'penerimaan_pangan_file' => ['required', 'file', 'mimes:pdf', "max:$maxSize"],
            'hasil_masak_file' => ['required', 'file', 'mimes:pdf', "max:$maxSize"],
            'sj_sekolah_file' => ['required', 'file', 'mimes:pdf', "max:$maxSize"],
            'counter_ompreng_file' => ['required', 'file', 'mimes:pdf', "max:$maxSize"],
            
            // Optional files (3)
            'sj_kp_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'penerimaan_non_pangan_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'gudang_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
        ];
    }

    public function messages()
    {
        return [
            'po_file.required' => 'File PO harus diupload',
            'po_file.mimes' => 'File PO harus format PDF',
            'po_file.max' => 'File PO tidak boleh lebih dari 10MB',
            
            'invoice_file.required' => 'File Invoice harus diupload',
            'invoice_file.mimes' => 'File Invoice harus format PDF',
            'invoice_file.max' => 'File Invoice tidak boleh lebih dari 10MB',
            
            'penerimaan_pangan_file.required' => 'File Penerimaan Pangan harus diupload',
            'penerimaan_pangan_file.mimes' => 'File Penerimaan Pangan harus format PDF',
            'penerimaan_pangan_file.max' => 'File Penerimaan Pangan tidak boleh lebih dari 10MB',
            
            'hasil_masak_file.required' => 'File Hasil Masak harus diupload',
            'hasil_masak_file.mimes' => 'File Hasil Masak harus format PDF',
            'hasil_masak_file.max' => 'File Hasil Masak tidak boleh lebih dari 10MB',
            
            'sj_sekolah_file.required' => 'File SJ Sekolah harus diupload',
            'sj_sekolah_file.mimes' => 'File SJ Sekolah harus format PDF',
            'sj_sekolah_file.max' => 'File SJ Sekolah tidak boleh lebih dari 10MB',
            
            'counter_ompreng_file.required' => 'File Counter Ompreng harus diupload',
            'counter_ompreng_file.mimes' => 'File Counter Ompreng harus format PDF',
            'counter_ompreng_file.max' => 'File Counter Ompreng tidak boleh lebih dari 10MB',
            
            'sj_kp_file.mimes' => 'File SJ/KP harus format PDF',
            'sj_kp_file.max' => 'File SJ/KP tidak boleh lebih dari 10MB',
            
            'penerimaan_non_pangan_file.mimes' => 'File Penerimaan Non-Pangan harus format PDF',
            'penerimaan_non_pangan_file.max' => 'File Penerimaan Non-Pangan tidak boleh lebih dari 10MB',
            
            'gudang_file.mimes' => 'File Gudang harus format PDF',
            'gudang_file.max' => 'File Gudang tidak boleh lebih dari 10MB',
            
            'tanggal_pelayanan.required' => 'Tanggal pelayanan harus diisi',
            'tanggal_pelayanan.date' => 'Format tanggal tidak valid',
            'id_menu.exists' => 'Menu yang dipilih tidak valid',
        ];
    }
}

/**
 * Form Request untuk update upload data
 */
class UpdateUploadDataRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $maxSize = '10240'; // 10MB
        
        return [
            'tanggal_pelayanan' => ['required', 'date'],
            'id_menu' => ['nullable', 'exists:tb_menu,id'],
            
            // File updates - semua optional
            'menu_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'po_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'invoice_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'penerimaan_pangan_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'hasil_masak_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'sj_sekolah_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'counter_ompreng_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'sj_kp_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'penerimaan_non_pangan_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
            'gudang_file' => ['nullable', 'file', 'mimes:pdf', "max:$maxSize"],
        ];
    }

    public function messages()
    {
        return [
            '*.mimes' => 'File harus format PDF',
            '*.max' => 'File tidak boleh lebih dari 10MB',
        ];
    }
}

/**
 * Form Request untuk surat jalan item
 */
class StoreSuratJalanItemRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'referensi' => ['required', 'string'],
            'items' => ['required', 'array'],
            'items.*.rincian_sekolah_id' => ['required', 'exists:rincian_sekolah,id'],
            'items.*.jumlah_penerima_a' => ['required', 'integer', 'min:0'],
            'items.*.jumlah_penerima_b' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Minimal harus ada 1 sekolah yang dipilih',
            'items.*.rincian_sekolah_id.required' => 'Data sekolah tidak valid',
            'items.*.jumlah_penerima_a.required' => 'Jumlah penerima A harus diisi',
            'items.*.jumlah_penerima_a.integer' => 'Jumlah penerima A harus angka',
            'items.*.jumlah_penerima_b.required' => 'Jumlah penerima B harus diisi',
            'items.*.jumlah_penerima_b.integer' => 'Jumlah penerima B harus angka',
        ];
    }
}
