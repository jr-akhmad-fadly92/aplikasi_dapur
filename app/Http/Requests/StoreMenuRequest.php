<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
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
            'nama_menu' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jenis_menu' => 'required|in:pagi,siang,sore',
            'jumlah_porsi' => 'required|integer|min:1',
            'tingkat_sekolah' => 'required|exists:tb_tingkat_sekolah,id',
            'keterangan' => 'nullable|string|max:500',
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
            'nama_menu.required' => 'Nama menu harus diisi',
            'nama_menu.max' => 'Nama menu maksimal 255 karakter',
            'tanggal.required' => 'Tanggal menu harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'jenis_menu.required' => 'Jenis menu harus dipilih',
            'jenis_menu.in' => 'Jenis menu tidak valid',
            'jumlah_porsi.required' => 'Jumlah porsi harus diisi',
            'jumlah_porsi.integer' => 'Jumlah porsi harus berupa angka',
            'jumlah_porsi.min' => 'Jumlah porsi minimal 1',
            'tingkat_sekolah.required' => 'Tingkat sekolah harus dipilih',
            'tingkat_sekolah.exists' => 'Tingkat sekolah tidak valid',
        ];
    }
}
