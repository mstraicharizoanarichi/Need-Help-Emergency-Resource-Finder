<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BloodDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'blood_group' => ['required', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'phone' => ['required', 'string', 'max:30'],
            'alternative_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'district' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_available' => ['nullable', 'boolean'],
            'last_donation_date' => ['nullable', 'date', 'before_or_equal:today'],
            'total_donations' => ['nullable', 'integer', 'min:0'],
            'privacy_setting' => ['required', 'in:contact_request,show_on_request,direct_call'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
