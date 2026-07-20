<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_preferences', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('transfer_application_id')
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->unsignedTinyInteger('preference_order');

            $table
                ->foreignId('school_id')
                ->constrained()
                ->restrictOnDelete();

            $table
                ->text('preference_reason')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'transfer_application_id',
                'preference_order',
            ], 'application_preference_order_unique');

            $table->unique([
                'transfer_application_id',
                'school_id',
            ], 'application_preference_school_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_preferences');
    }
};
