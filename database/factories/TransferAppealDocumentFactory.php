<?php

namespace Database\Factories;

use App\Models\TransferAppeal;
use App\Models\TransferAppealDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferAppealDocument>
 */
class TransferAppealDocumentFactory extends Factory
{
    protected $model = TransferAppealDocument::class;

    public function definition(): array
    {
        return [
            'transfer_appeal_id' => TransferAppeal::factory(),
            'document_name' => 'Supporting Document',
            'original_name' => 'supporting-document.pdf',
            'file_path' => 'transfer-appeals/test/supporting-document.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_by' => User::factory(),
        ];
    }
}
