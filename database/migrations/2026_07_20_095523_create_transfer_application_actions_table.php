<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_application_actions', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('transfer_application_id')
                ->constrained('transfer_applications')
                ->cascadeOnDelete();

            $table->string('action', 100);
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50)->nullable();
            $table->text('remarks')->nullable();

            $table
                ->foreignId('acted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('acted_at');
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['transfer_application_id', 'acted_at'],
                'transfer_app_actions_app_acted_idx'
            );

            $table->index(
                ['action', 'acted_at'],
                'transfer_app_actions_action_acted_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_application_actions');
    }
};
