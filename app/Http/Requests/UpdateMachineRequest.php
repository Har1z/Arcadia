<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMachineRequest extends FormRequest
{
    /**
     * Both roles may hit this request: admins edit everything,
     * staff only the status field (enforced by the rules below).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Staff may ONLY send the status field: every other field is
     * 'prohibited', so a crafted staff payload is rejected instead of
     * silently ignored. Admins get the full rule set.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isAdmin = $this->user()->isAdmin();
        $staffOnlyStatus = $isAdmin ? 'required' : 'prohibited';

        return [
            'category_id' => [$staffOnlyStatus, 'integer', 'exists:categories,id'],
            'name' => [$staffOnlyStatus, 'string', 'max:255'],
            'serial_number' => [$staffOnlyStatus, 'string', 'max:100', Rule::unique('machines', 'serial_number')->ignore($this->route('machine'))],
            'location' => [$staffOnlyStatus, 'string', 'max:255'],
            'purchase_date' => [$staffOnlyStatus, 'date'],
            'purchase_price' => [$staffOnlyStatus, 'numeric', 'min:0'],
            'price_per_play' => [$staffOnlyStatus, 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', StoreMachineRequest::statuses())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.prohibited' => 'Anda hanya boleh mengubah status mesin.',
            'name.prohibited' => 'Anda hanya boleh mengubah status mesin.',
            'serial_number.prohibited' => 'Anda hanya boleh mengubah status mesin.',
            'location.prohibited' => 'Anda hanya boleh mengubah status mesin.',
            'purchase_date.prohibited' => 'Anda hanya boleh mengubah status mesin.',
            'purchase_price.prohibited' => 'Anda hanya boleh mengubah status mesin.',
            'price_per_play.prohibited' => 'Anda hanya boleh mengubah status mesin.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
