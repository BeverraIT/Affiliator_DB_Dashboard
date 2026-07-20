<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCreatorRequest extends FormRequest
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
            'is_blocked' => $this->has('is_blocked') ? 1 : 0,
        ]);
    }

    public function rules(): array
    {
        $creator = $this->route('creator') ?? $this->route('kreator');

        return [
            'username' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9._]+$/', Rule::unique('creators', 'username')->ignore($creator->id)],
            'top_creator' => ['boolean'],
            'is_blocked' => ['boolean'],
            'channel' => ['required', Rule::in(['video', 'live', 'kartu produk'])],
            'genderpengikut' => ['required', Rule::in(['laki-laki', 'perempuan', 'seimbang'])],
            'gmv' => ['required', 'integer', 'min:0'],
            'itemsterjual' => ['required', 'integer', 'min:0'],
            'namawa' => ['nullable', 'string', 'max:150'],
            'nomerwa' => ['nullable', 'string', 'max:150'],
            'ages' => ['nullable', 'array'],
            'ages.*' => ['integer', Rule::exists('ages', 'id')],
            'categories' => ['nullable', 'array', 'max:5'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')],
            'contentstyles' => ['nullable', 'array', 'max:9'],
            'contentstyles.*' => ['integer', Rule::exists('contentstyles', 'id')],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh kreator lain.',
            'categories.max' => 'Kategori maksimal 5.',
            'contentstyles.max' => 'Konten style maksimal 9.',
        ];
    }
}
