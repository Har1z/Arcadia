<?php

namespace App\Http\Requests;

use App\Models\Machine;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMachineRequest extends FormRequest
{
    /**
     * Route middleware ('role:admin') already limits this to admins.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:100', 'unique:machines,serial_number'],
            'location' => ['required', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'price_per_play' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', self::statuses())],
        ];
    }

    /**
     * Indonesian error messages (UI language is Indonesian).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'name.required' => 'Nama mesin wajib diisi.',
            'name.max' => 'Nama mesin maksimal 255 karakter.',
            'serial_number.required' => 'Nomor seri wajib diisi.',
            'serial_number.unique' => 'Nomor seri sudah digunakan mesin lain.',
            'location.required' => 'Lokasi wajib diisi.',
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'purchase_date.date' => 'Tanggal pembelian tidak valid.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'price_per_play.required' => 'Harga per main wajib diisi.',
            'price_per_play.numeric' => 'Harga per main harus berupa angka.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            Machine::STATUS_ACTIVE,
            Machine::STATUS_MAINTENANCE,
            Machine::STATUS_BROKEN,
            Machine::STATUS_RETIRED,
        ];
    }
}
