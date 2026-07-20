<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadSignedTransferDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can(
                'upload signed transfer documents'
            )
            ?? false;
    }

    public function rules(): array
    {
        return [
            'signed_document' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'signed_document.required' =>
                'Please select a signed PDF document.',

            'signed_document.mimes' =>
                'The signed document must be a PDF file.',

            'signed_document.max' =>
                'The signed document may not exceed 10 MB.',
        ];
    }
}
