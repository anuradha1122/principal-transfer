<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('zone_id')
                ->constrained()
                ->restrictOnDelete();

            $table
                ->string('name', 150);

            $table
                ->string('code', 30)
                ->unique();

            $table
                ->string('office_address')
                ->nullable();

            $table
                ->string('telephone', 30)
                ->nullable();

            $table
                ->string('email')
                ->nullable();

            $table
                ->boolean('is_active')
                ->default(true);

            $table
                ->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique([
                'zone_id',
                'name',
            ]);

            $table->index([
                'zone_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
