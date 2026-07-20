<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSendSampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'creator_id' => ['required', 'exists:creators,id'],
            'store_id' => ['required', 'exists:stores,id'],
            'product_id' => ['required', 'exists:products,id'],
            'seller_product_id' => ['nullable', 'string'],
            'pic' => ['required', 'exists:users,id'],
            'approve' => ['required', 'exists:users,id'],
            'deal_date' => ['nullable', 'date'],
            'tanggal_kirim' => ['required', 'date'],
            'tanggal_aktual' => ['nullable', 'date', 'after_or_equal:tanggal_kirim'],
            'qty' => ['required', 'integer', 'min:1'],
            'id_pesanan' => ['nullable', 'string', 'max:255'],
            'perjanjian_livestream' => ['nullable', 'integer', 'min:0'],
            'perjanjian_video' => ['nullable', 'integer', 'min:0'],
            'live_dibuat' => ['nullable', 'integer', 'min:0'],
            'video_dibuat' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'creator_id' => 'kreator',
            'store_id' => 'toko',
            'product_id' => 'produk',
            'pic' => 'PIC',
        ];
    }
}
