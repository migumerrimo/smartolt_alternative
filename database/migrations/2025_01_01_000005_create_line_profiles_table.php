<?php
// database/migrations/2025_01_01_000005_create_line_profiles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('line_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->string('name', 50);
            $table->foreignId('dba_profile_id')->nullable()->constrained('dba_profiles')->nullOnDelete();
            $table->integer('tcont');
            $table->integer('gem_ports');
            $table->foreignId('vlan_id')->nullable()->constrained('vlans')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('line_profiles');
    }
};
