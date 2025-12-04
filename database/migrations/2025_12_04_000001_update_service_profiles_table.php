<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la tabla ya existe, agregar columnas faltantes
        if (Schema::hasTable('service_profiles')) {
            Schema::table('service_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('service_profiles', 'profile_id')) {
                    $table->integer('profile_id')->nullable()->after('olt_id')->comment('Profile ID from OLT device');
                }
                if (!Schema::hasColumn('service_profiles', 'binding_times')) {
                    $table->integer('binding_times')->nullable()->after('eth_ports')->comment('Binding times from OLT');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_profiles')) {
            Schema::table('service_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('service_profiles', 'profile_id')) {
                    $table->dropColumn('profile_id');
                }
                if (Schema::hasColumn('service_profiles', 'binding_times')) {
                    $table->dropColumn('binding_times');
                }
            });
        }
    }
};
