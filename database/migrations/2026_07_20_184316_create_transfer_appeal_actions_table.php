<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_appeal_actions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transfer_appeal_id')
                ->constrained('transfer_appeals')
                ->cascadeOnDelete();

            $table->string('action', 100);
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40)->nullable();
            $table->text('remarks')->nullable();

            $table->foreignId('acted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('acted_at');
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['transfer_appeal_id', 'acted_at'],
                'taa_appeal_acted_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_appeal_actions');
    }
};
