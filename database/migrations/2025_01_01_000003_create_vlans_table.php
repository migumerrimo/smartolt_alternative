<?php
// database/migrations/2025_01_01_000003_create_vlans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vlans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->integer('number');
            $table->enum('type', ['standard','smart','mux','super'])->default('standard');
            $table->string('description', 255)->nullable();
            $table->string('uplink_port', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('vlans');
    }
};

