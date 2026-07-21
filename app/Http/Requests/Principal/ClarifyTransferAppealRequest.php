<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;

class ClarifyTransferAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('submit transfer appeals') ?? false;
    }

    public function rules(): array
    {
        $maximumDocuments = config('transfer-appeals.maximum_documents', 5);
        $maximumSize = config(
            'transfer-appeals.maximum_document_size_kb',
            5120
        );

        return [
            'clarification_response' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],
            'documents' => [
                'nullable',
                'array',
                'max:'.$maximumDocuments,
            ],
            'documents.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:'.$maximumSize,
            ],
        ];
    }
}
