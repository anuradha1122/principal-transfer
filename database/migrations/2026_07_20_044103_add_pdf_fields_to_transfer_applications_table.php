<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'transfer_applications',
            function (Blueprint $table): void {
                $table
                    ->string('submitted_pdf_path')
                    ->nullable()
                    ->after('submitted_at');

                $table
                    ->timestamp('submitted_pdf_generated_at')
                    ->nullable()
                    ->after('submitted_pdf_path');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'transfer_applications',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'submitted_pdf_path',
                    'submitted_pdf_generated_at',
                ]);
            }
        );
    }
};
