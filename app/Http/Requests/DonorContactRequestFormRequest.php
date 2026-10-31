<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonorContactRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('hospital_location') || empty($this->input('hospital_location'))) {
            $this->merge([
                'hospital_location' => $this->input('hospital_name', 'Dhaka'),
            ]);
        }

        $urgency = $this->input('urgency_level');
        if ($urgency === 'critical') {
            $this->merge(['urgency_level' => 'emergency']);
        } elseif ($urgency === 'standard') {
            $this->merge(['urgency_level' => 'urgent']);
        }
    }

    public function rules(): array
    {
        return [
            'blood_donor_id' => ['required', 'exists:blood_donors,id'],
            'requester_name' => ['required', 'string', 'max:150'],
            'requester_phone' => ['required', 'string', 'max:30'],
            'patient_condition' => ['nullable', 'string', 'max:255'],
            'patient_name' => ['nullable', 'string', 'max:255'],
            'hospital_name' => ['required', 'string', 'max:255'],
            'hospital_location' => ['nullable', 'string', 'max:255'],
            'urgency_level' => ['required', 'in:urgent,very_urgent,emergency'],
            'units_needed' => ['required', 'integer', 'min:1', 'max:10'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
