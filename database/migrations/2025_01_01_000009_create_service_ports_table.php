<?php
// database/migrations/2025_01_01_000009_create_service_ports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->foreignId('onu_id')->constrained('onus')->cascadeOnDelete();
            $table->foreignId('vlan_id')->constrained('vlans')->cascadeOnDelete();
            $table->foreignId('traffic_table_id')->nullable()->constrained('traffic_table')->nullOnDelete();
            $table->integer('gemport_id')->nullable();
            $table->enum('type', ['gpon','eth','epon'])->default('gpon');
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('service_ports');
    }
};
