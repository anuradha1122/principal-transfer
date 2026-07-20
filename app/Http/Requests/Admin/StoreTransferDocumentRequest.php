<?php

namespace App\Http\Requests\Admin;

use App\Models\TransferDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransferDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can(
                'generate transfer documents'
            )
            ?? false;
    }

    public function rules(): array
    {
        return [
            'transfer_application_id' => [
                'required',
                'integer',
                'exists:transfer_applications,id',
            ],

            'document_type' => [
                'required',
                'string',
                Rule::in(
                    TransferDocument::TYPES
                ),
            ],

            'document_number' => [
                'required',
                'string',
                'max:150',
                Rule::unique(
                    'transfer_documents',
                    'document_number'
                ),
            ],

            'issued_date' => [
                'required',
                'date',
            ],

            'effective_date' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
