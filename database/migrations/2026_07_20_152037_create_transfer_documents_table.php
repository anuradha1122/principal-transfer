<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'transfer_documents',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('transfer_application_id')
                    ->constrained('transfer_applications')
                    ->cascadeOnDelete();

                $table
                    ->enum('document_type', [
                        'Transfer Order',
                        'Appointment Letter',
                        'Decision Letter',
                    ]);

                $table
                    ->string('document_number', 150)
                    ->unique();

                $table
                    ->date('issued_date');

                $table
                    ->date('effective_date')
                    ->nullable();

                $table
                    ->string('generated_file_path')
                    ->nullable();

                $table
                    ->string('signed_file_path')
                    ->nullable();

                $table
                    ->foreignId('issued_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->timestamp('generated_at')
                    ->nullable();

                $table
                    ->boolean('is_published')
                    ->default(false);

                $table
                    ->timestamp('published_at')
                    ->nullable();

                $table
                    ->foreignId('published_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->text('remarks')
                    ->nullable();

                $table->timestamps();

                $table->unique(
                    [
                        'transfer_application_id',
                        'document_type',
                    ],
                    'transfer_doc_application_type_unique'
                );

                $table->index(
                    [
                        'is_published',
                        'published_at',
                    ],
                    'transfer_doc_publish_idx'
                );

                $table->index(
                    [
                        'document_type',
                        'issued_date',
                    ],
                    'transfer_doc_type_date_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'transfer_documents'
        );
    }
};
