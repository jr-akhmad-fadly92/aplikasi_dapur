<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenerimaanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_po' => 'required|exists:tb_po,id',
            'tanggal_penerimaan' => 'required|date',
            'id_supplier' => 'required|exists:tb_supplier,id',
            'no_kendaraan' => 'nullable|string|max:20',
            'nama_pengantar' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:tb_bahan,id',
            'items.*.jumlah' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|string',
            'items.*.harga' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id_po.required' => 'PO harus dipilih',
            'id_po.exists' => 'PO tidak valid',
            'tanggal_penerimaan.required' => 'Tanggal penerimaan harus diisi',
            'tanggal_penerimaan.date' => 'Format tanggal tidak valid',
            'id_supplier.required' => 'Supplier harus dipilih',
            'id_supplier.exists' => 'Supplier tidak valid',
            'items.required' => 'Item penerimaan harus diisi',
            'items.min' => 'Minimal 1 item harus diisi',
            'items.*.id_bahan.required' => 'Bahan harus dipilih',
            'items.*.id_bahan.exists' => 'Bahan tidak valid',
            'items.*.jumlah.required' => 'Jumlah harus diisi',
            'items.*.jumlah.numeric' => 'Jumlah harus berupa angka',
            'items.*.jumlah.min' => 'Jumlah tidak boleh negatif',
        ];
    }
}
