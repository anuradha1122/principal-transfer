<?php

namespace Database\Factories;

use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferDocumentFactory extends Factory
{
    protected $model =
        TransferDocument::class;

    public function definition(): array
    {
        return [
            'transfer_application_id' =>
                TransferApplication::factory(),

            'document_type' =>
                TransferDocument::TYPE_DECISION_LETTER,

            'document_number' =>
                fake()->unique()->bothify(
                    'DEC/2026/####'
                ),

            'issued_date' =>
                now()->toDateString(),

            'effective_date' =>
                null,

            'generated_file_path' =>
                null,

            'signed_file_path' =>
                null,

            'issued_by' =>
                User::factory(),

            'generated_at' =>
                now(),

            'is_published' =>
                false,

            'published_at' =>
                null,

            'published_by' =>
                null,

            'remarks' =>
                fake()->optional()->sentence(),
        ];
    }

    public function published(): static
    {
        return $this->state(
            fn (): array => [
                'is_published' =>
                    true,

                'published_at' =>
                    now(),

                'published_by' =>
                    User::factory(),
            ]
        );
    }
}
