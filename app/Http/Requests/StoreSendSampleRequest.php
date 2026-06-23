<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSendSampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'creator_id'            => ['required'],
            'pic'                   => ['required'],
            'approve'               => ['required'],
            'store_id'              => ['required', 'exists:stores,id'],
            'product_id'            => ['required', 'exists:products,id'],
            'seller_product_id'     => ['nullable', 'string'],
            'deal_date'             => ['required', 'date'],
            'tanggal_kirim'         => ['required', 'date', 'after_or_equal:deal_date'],
            'estimasi_sampai'       => ['nullable', 'date', 'after_or_equal:tanggal_kirim'],
            'tanggal_aktual'        => ['nullable', 'date'],
            'qty'                   => ['required', 'integer', 'min:1'],
            'id_pesanan'            => ['nullable', 'string', 'max:255'],
            'perjanjian_livestream' => ['nullable', 'integer', 'min:0'],
            'perjanjian_video'      => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'creator_id' => 'kreator',
            'store_id'   => 'toko',
            'product_id' => 'produk',
        ];
    }
}