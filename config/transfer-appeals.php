<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Appeal deadline
    |--------------------------------------------------------------------------
    |
    | Number of calendar days from final-result publication during which a
    | Principal may create and submit an appeal.
    |
    */

    'deadline_days' => (int) env('TRANSFER_APPEAL_DEADLINE_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Supporting documents
    |--------------------------------------------------------------------------
    */

    'maximum_documents' => (int) env('TRANSFER_APPEAL_MAX_DOCUMENTS', 5),

    'maximum_document_size_kb' => (int) env(
        'TRANSFER_APPEAL_MAX_DOCUMENT_SIZE_KB',
        5120
    ),

    'allowed_document_mimetypes' => [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ],
];
