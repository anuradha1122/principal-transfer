<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'principal_profiles',
            function (Blueprint $table): void {
                $table
                    ->string(
                        'service_category',
                        150
                    )
                    ->nullable()
                    ->change();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'principal_profiles',
            function (Blueprint $table): void {
                $table
                    ->enum(
                        'service_category',
                        [
                            'Principal Service',
                            'Education Administrative Service',
                            'Other',
                        ]
                    )
                    ->nullable()
                    ->change();
            }
        );
    }
};
