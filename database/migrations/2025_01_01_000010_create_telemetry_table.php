<?php
// database/migrations/2025_01_01_000010_create_telemetry_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('telemetry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->foreignId('onu_id')->nullable()->constrained('onus')->nullOnDelete();
            $table->string('metric', 50);
            $table->decimal('value', 10, 3);
            $table->string('unit', 20)->nullable();
            $table->timestamp('sampled_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('telemetry');
    }
};
