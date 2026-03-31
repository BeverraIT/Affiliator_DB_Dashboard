<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku_utama' => ['required', 'string', 'max:150'],
            'sku'       => ['required', 'string', 'max:150', 'unique:products,sku'],
            'harga'     => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sku_utama' => trim((string) $this->input('sku_utama')),
            'sku'       => trim((string) $this->input('sku')),
        ]);
    }


    public function messages(): array
    {
        return [
            'sku_utama.required' => 'SKU Utama wajib diisi.',
            'sku.required'       => 'SKU Varian wajib diisi.',
            'sku.unique'         => 'SKU Varian ini sudah terdaftar, gunakan kode lain.',
            'harga.required'     => 'Harga wajib diisi.',
            'harga.numeric'      => 'Harga harus berupa angka.',
            'harga.min'          => 'Harga tidak boleh kurang dari 0.',
        ];
    }
}