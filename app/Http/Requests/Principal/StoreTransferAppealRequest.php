<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create transfer appeals') ?? false;
    }

    public function rules(): array
    {
        $maximumDocuments = config('transfer-appeals.maximum_documents', 5);
        $maximumSize = config(
            'transfer-appeals.maximum_document_size_kb',
            5120
        );

        return [
            'transfer_application_id' => [
                'required',
                'integer',
                'exists:transfer_applications,id',
            ],
            'appeal_reason' => [
                'required',
                'string',
                'max:150',
            ],
            'appeal_details' => [
                'required',
                'string',
                'min:20',
                'max:10000',
            ],
            'requested_outcome' => [
                'required',
                'string',
                'min:10',
                'max:5000',
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

    public function attributes(): array
    {
        return [
            'transfer_application_id' => 'transfer application',
            'appeal_reason' => 'appeal reason',
            'appeal_details' => 'appeal details',
            'requested_outcome' => 'requested outcome',
            'documents.*' => 'supporting document',
        ];
    }
}
