<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            /*
             * A UUID shared by audit records generated during the same HTTP
             * request. This makes one user action easier to trace.
             */
            $table->uuid('request_id')->nullable();

            /*
             * High-level classification.
             *
             * Examples:
             * model
             * workflow
             * authentication
             * document
             * security
             */
            $table->string('category', 50)->default('model');

            /*
             * Human-readable action identifier.
             *
             * Examples:
             * created
             * updated
             * deleted
             * transfer_application.submitted
             * transfer_appeal.approved
             */
            $table->string('event', 150);

            $table->string('description', 500)->nullable();

            /*
             * Polymorphic subject fields are stored manually instead of using
             * morphs() so historical records survive even if the source model
             * is later removed.
             */
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();

            /*
             * Optional parent entity. This helps connect a document, review,
             * decision, or appeal action back to its main application.
             */
            $table->string('parent_type')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Snapshot of the actor's identity and roles at action time.
             * This remains useful if the user is later renamed or removed.
             */
            $table->string('actor_name')->nullable();
            $table->string('actor_email')->nullable();
            $table->json('actor_roles')->nullable();

            $table->string('old_status', 100)->nullable();
            $table->string('new_status', 100)->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();

            $table->string('route_name')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('occurred_at');

            /*
             * Standard timestamps are useful for database administration,
             * while occurred_at represents the actual audited event time.
             */
            $table->timestamps();

            $table->index(
                ['auditable_type', 'auditable_id'],
                'audit_subject_idx'
            );

            $table->index(
                ['parent_type', 'parent_id'],
                'audit_parent_idx'
            );

            $table->index(
                ['user_id', 'occurred_at'],
                'audit_user_time_idx'
            );

            $table->index(
                ['category', 'event'],
                'audit_category_event_idx'
            );

            $table->index(
                ['request_id'],
                'audit_request_idx'
            );

            $table->index(
                ['occurred_at'],
                'audit_occurred_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
