<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContentStyleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('contentStyle')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('contentstyles', 'name')->ignore($id),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama konten style wajib diisi.',
            'name.unique'   => 'Nama konten style sudah ada.',
        ];
    }
}