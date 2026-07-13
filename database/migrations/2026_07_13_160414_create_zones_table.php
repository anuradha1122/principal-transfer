<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table): void {
            $table->id();

            $table
                ->string('name', 150)
                ->unique();

            $table
                ->string('code', 20)
                ->unique();

            $table
                ->string('district', 50);

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

            $table->index([
                'district',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
