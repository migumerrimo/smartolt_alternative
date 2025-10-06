<?php
// database/migrations/2025_01_01_000006_create_service_profiles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->string('name', 50);
            $table->enum('service', ['internet','voip','iptv','triple-play'])->default('internet');
            $table->integer('eth_ports');
            $table->foreignId('vlan_id')->nullable()->constrained('vlans')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('service_profiles');
    }
};
