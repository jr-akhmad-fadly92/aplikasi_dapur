<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePoRequest extends FormRequest
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
            'id_kontrak' => 'required|exists:tb_kontrak,id',
            'tanggal_po' => 'required|date',
            'id_menu' => 'required|exists:tb_master_menu,id',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|exists:tb_bahan,id',
            'items.*.jumlah' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
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
            'id_kontrak.required' => 'Kontrak harus dipilih',
            'id_kontrak.exists' => 'Kontrak tidak valid',
            'tanggal_po.required' => 'Tanggal PO harus diisi',
            'tanggal_po.date' => 'Format tanggal tidak valid',
            'id_menu.required' => 'Menu harus dipilih',
            'id_menu.exists' => 'Menu tidak valid',
            'items.required' => 'Item PO harus diisi',
            'items.min' => 'Minimal 1 item harus diisi',
            'items.*.id_bahan.required' => 'Bahan harus dipilih',
            'items.*.id_bahan.exists' => 'Bahan tidak valid',
            'items.*.jumlah.required' => 'Jumlah harus diisi',
            'items.*.jumlah.numeric' => 'Jumlah harus berupa angka',
            'items.*.jumlah.min' => 'Jumlah tidak boleh negatif',
            'items.*.harga.required' => 'Harga harus diisi',
            'items.*.harga.numeric' => 'Harga harus berupa angka',
            'items.*.harga.min' => 'Harga tidak boleh negatif',
        ];
    }
}
