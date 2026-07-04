<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'type'         => ['required', new Enum(TaskType::class)],
            'status'       => ['required', new Enum(TaskStatus::class)],
            'priority'     => ['required', new Enum(TaskPriority::class)],
            'sprint_id'    => ['nullable', 'exists:sprints,id'],
            'assignee_id'  => ['nullable', 'exists:users,id'],
            'wiki_page_id' => ['nullable', 'exists:wiki_pages,id'],
            'due_date'     => ['nullable', 'date'],
        ];
    }
}
