<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Si la tabla existe, agregamos las nuevas columnas
        if (Schema::hasTable('dba_profiles')) {
            Schema::table('dba_profiles', function (Blueprint $table) {
                // Agregamos las columnas si no existen
                if (!Schema::hasColumn('dba_profiles', 'profile_id')) {
                    $table->integer('profile_id')->nullable()->unique();
                }
                if (!Schema::hasColumn('dba_profiles', 'type')) {
                    $table->enum('type', ['1', '2', '3', '4'])->nullable();
                }
                if (!Schema::hasColumn('dba_profiles', 'bandwidth_compensation')) {
                    $table->string('bandwidth_compensation', 10)->nullable();
                }
                if (!Schema::hasColumn('dba_profiles', 'fix_kbps')) {
                    $table->integer('fix_kbps')->nullable()->default(0);
                }
                if (!Schema::hasColumn('dba_profiles', 'assure_kbps')) {
                    $table->integer('assure_kbps')->nullable()->default(0);
                }
                if (!Schema::hasColumn('dba_profiles', 'max_kbps')) {
                    $table->integer('max_kbps')->nullable()->default(0);
                }
                if (!Schema::hasColumn('dba_profiles', 'bind_times')) {
                    $table->integer('bind_times')->nullable()->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('dba_profiles')) {
            Schema::table('dba_profiles', function (Blueprint $table) {
                $columns = ['profile_id', 'type', 'bandwidth_compensation', 'fix_kbps', 'assure_kbps', 'max_kbps', 'bind_times'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('dba_profiles', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
