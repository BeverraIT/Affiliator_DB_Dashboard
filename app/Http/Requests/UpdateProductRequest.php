<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product'); 
        $productId = $product->id;

        return [
            'sku_utama'     => ['required', 'string', 'max:100'],
            'sku'           => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'harga'         => ['required', 'numeric', 'min:0'],
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
            'sku.unique'         => 'SKU Varian ini sudah digunakan oleh produk lain.',
            'harga.required'     => 'Harga wajib diisi.',
            'harga.numeric'      => 'Harga harus berupa angka.',
        ];
    }
}