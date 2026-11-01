<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isAdmin() || $this->user()->isProvider()) && $this->user()->isActive();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('services_offered') && !$this->has('services_offered_text')) {
            $this->merge(['services_offered_text' => $this->input('services_offered')]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:resource_categories,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'address' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'phone' => ['required', 'string', 'max:50'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'availability_status' => ['required', 'in:available,unavailable,busy,closed,unknown'],
            'is_24_7' => ['nullable', 'boolean'],
            'operating_hours_text' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'verification_status' => ['nullable', 'in:verified,pending,rejected'],
            
            // Details
            'icu_available' => ['nullable', 'boolean'],
            'icu_beds_count' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'ambulance_available' => ['nullable', 'boolean'],
            'oxygen_available' => ['nullable', 'boolean'],
            'blood_bank_available' => ['nullable', 'boolean'],
            'emergency_ward' => ['nullable', 'boolean'],
            'general_beds_count' => ['nullable', 'integer', 'min:0', 'max:50000'],
            'vehicle_types' => ['nullable', 'string', 'max:255'],
            'paramedic_onboard' => ['nullable', 'boolean'],
            'home_delivery' => ['nullable', 'boolean'],
            'online_reports' => ['nullable', 'boolean'],
            'services_offered' => ['nullable', 'string', 'max:1000'],
            'services_offered_text' => ['nullable', 'string', 'max:1000'],
            'facilities_text' => ['nullable', 'string', 'max:1000'],
            'additional_info' => ['nullable', 'string', 'max:2000'],
            'operating_hours' => ['nullable', 'array'],
        ];
    }
}
