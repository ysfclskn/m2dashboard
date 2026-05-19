<?php

namespace App\Http\Requests;

use App\Enums\WikiPageCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateWikiPageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'    => ['required', 'string', 'max:255'],
            'category' => ['required', new Enum(WikiPageCategory::class)],
            'content'  => ['nullable', 'string'],
        ];
    }
}
