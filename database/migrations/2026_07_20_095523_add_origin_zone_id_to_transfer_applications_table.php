<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'transfer_applications',
            function (Blueprint $table): void {
                $table
                    ->foreignId('origin_zone_id')
                    ->nullable()
                    ->after('current_school_id')
                    ->constrained('zones')
                    ->restrictOnDelete();

                $table->index(
                    [
                        'origin_zone_id',
                        'status',
                    ],
                    'transfer_apps_origin_zone_status_idx'
                );
            }
        );

        /*
         * Backfill existing applications using Laravel queries instead
         * of database-specific UPDATE JOIN syntax.
         *
         * This works with both MySQL and SQLite test databases.
         */
        DB::table('transfer_applications')
            ->whereNull('origin_zone_id')
            ->whereNotNull('current_school_id')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($applications): void {
                    foreach ($applications as $application) {
                        $zoneId = DB::table('schools')
                            ->join(
                                'divisions',
                                'divisions.id',
                                '=',
                                'schools.division_id'
                            )
                            ->where(
                                'schools.id',
                                $application->current_school_id
                            )
                            ->value('divisions.zone_id');

                        if ($zoneId === null) {
                            continue;
                        }

                        DB::table('transfer_applications')
                            ->where(
                                'id',
                                $application->id
                            )
                            ->update([
                                'origin_zone_id' => $zoneId,
                            ]);
                    }
                },
                'id'
            );
    }

    public function down(): void
    {
        Schema::table(
            'transfer_applications',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'transfer_apps_origin_zone_status_idx'
                );

                $table->dropConstrainedForeignId(
                    'origin_zone_id'
                );
            }
        );
    }
};
