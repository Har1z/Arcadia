<?php

namespace App\Http\Requests;

use App\Models\MaintenanceLog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceLogRequest extends FormRequest
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
            'issue' => ['required', 'string', 'max:255'],
            'action_taken' => ['nullable', 'string', 'max:1000'],
            'cost' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', self::statuses())],
            'date' => ['required', 'date'],
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
            'issue.required' => 'Masalah wajib diisi.',
            'issue.max' => 'Masalah maksimal 255 karakter.',
            'action_taken.max' => 'Tindakan maksimal 1000 karakter.',
            'cost.required' => 'Biaya wajib diisi.',
            'cost.numeric' => 'Biaya harus berupa angka.',
            'cost.min' => 'Biaya tidak boleh negatif.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal tidak valid.',
        ];
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            MaintenanceLog::STATUS_OPEN,
            MaintenanceLog::STATUS_DONE,
        ];
    }
}
