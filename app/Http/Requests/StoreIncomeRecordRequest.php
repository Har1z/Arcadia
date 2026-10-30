<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRecordRequest extends FormRequest
{
    /**
     * The auth middleware guarantees a login; ownership is enforced by
     * the controller (the creator becomes the owner of the record).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'machine_id' => ['required', 'integer', 'exists:machines,id'],
            'date' => ['required', 'date'],
            'play_count' => ['required', 'integer', 'min:1'],
            'total_income' => ['required', 'numeric', 'min:0'],
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
            'machine_id.required' => 'Mesin wajib dipilih.',
            'machine_id.exists' => 'Mesin tidak ditemukan.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal tidak valid.',
            'play_count.required' => 'Jumlah permainan wajib diisi.',
            'play_count.integer' => 'Jumlah permainan harus berupa angka bulat.',
            'play_count.min' => 'Jumlah permainan minimal 1.',
            'total_income.required' => 'Total pendapatan wajib diisi.',
            'total_income.numeric' => 'Total pendapatan harus berupa angka.',
            'total_income.min' => 'Total pendapatan tidak boleh negatif.',
        ];
    }
}
