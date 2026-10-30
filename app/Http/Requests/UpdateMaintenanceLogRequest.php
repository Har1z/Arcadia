<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceLogRequest extends FormRequest
{
    /**
     * Both roles hit this request: the controller authorizes via the
     * policy first (admin or record owner), then validation runs.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Owners may edit every field of their own record; there is no
     * field-level restriction here because ownership is the gate.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'machine_id' => ['required', 'integer', 'exists:machines,id'],
            'issue' => ['required', 'string', 'max:255'],
            'action_taken' => ['nullable', 'string', 'max:1000'],
            'cost' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', StoreMaintenanceLogRequest::statuses())],
            'date' => ['required', 'date'],
        ];
    }

    /**
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
}
