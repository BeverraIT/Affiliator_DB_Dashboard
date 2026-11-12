<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCreatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => \App\Models\Creator::normalizeUsername((string) $this->username),
            ]);
        }

        $this->merge([
            'top_creator' => $this->has('top_creator') ? 1 : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9._]+$/', Rule::unique('creators', 'username')],
            'top_creator' => ['boolean'],
            'channel' => ['required', Rule::in(['video', 'live', 'kartu produk'])],
            'genderpengikut' => ['required', Rule::in(['perempuan', 'laki-laki', 'seimbang'])],
            'gmv' => ['required', 'integer', 'min:0'],
            'itemsterjual' => ['required', 'integer', 'min:0'],
            'namawa' => ['nullable', 'string', 'max:150'],
            'nomerwa' => ['nullable', 'string', 'max:150'],
            'ages' => ['nullable', 'array'],
            'ages.*' => ['integer', Rule::exists('ages', 'id')],
            'categories' => ['required', 'array', 'min:1', 'max:5'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')],
            'contentstyles' => ['nullable', 'array', 'max:9'],
            'contentstyles.*' => ['integer', Rule::exists('contentstyles', 'id')],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar di database kreator.',
            'username.regex' => 'Username hanya boleh huruf kecil (a-z), angka, titik, dan underscore, tanpa spasi.',
            'channel.required' => 'Channel wajib diisi.',
            'genderpengikut.required' => 'Gender pengikut wajib diisi.',
            'gmv.required' => 'GMV wajib diisi.',
            'itemsterjual.required' => 'Item terjual wajib diisi.',
            'namawa.required' => 'Nama WA wajib diisi.',
            'nomerwa.required' => 'Nomor WA wajib diisi.',
            'categories.required' => 'Kategori wajib dipilih (minimal 1).',
            'categories.min' => 'Kategori wajib dipilih (minimal 1).',
            'categories.max' => 'Kategori maksimal 5.',
            'contentstyles.max' => 'Konten style maksimal 9.',
        ];
    }
}
