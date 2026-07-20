<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PublishTransferDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can(
                'publish transfer results'
            )
            ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
