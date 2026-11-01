<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource_id' => ['nullable', 'exists:resources,id'],
            'blood_donor_id' => ['nullable', 'exists:blood_donors,id'],
            'reporter_name' => ['required', 'string', 'max:150'],
            'reporter_email' => ['nullable', 'email', 'max:150'],
            'reporter_phone' => ['nullable', 'string', 'max:30'],
            'report_type' => [
                'required',
                'in:incorrect_phone,wrong_location,closed_service,outdated_info,duplicate_resource,incorrect_availability,other'
            ],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
