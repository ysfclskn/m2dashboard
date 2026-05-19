<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgressReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'period_start_date' => ['required', 'date'],
            'period_end_date'   => ['required', 'date', 'after_or_equal:period_start_date'],
            'summary'           => ['required', 'string'],
            'completed_work'    => ['nullable', 'string'],
            'current_blockers'  => ['nullable', 'string'],
            'next_goals'        => ['nullable', 'string'],
        ];
    }
}
