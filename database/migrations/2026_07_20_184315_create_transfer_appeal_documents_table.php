<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_appeal_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transfer_appeal_id')
                ->constrained('transfer_appeals')
                ->cascadeOnDelete();

            $table->string('document_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('disk', 30)->default('local');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('transfer_appeal_id', 'tad_appeal_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_appeal_documents');
    }
};
