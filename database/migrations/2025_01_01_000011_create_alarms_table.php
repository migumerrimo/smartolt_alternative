<?php
// database/migrations/2025_01_01_000011_create_alarms_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alarms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->foreignId('onu_id')->nullable()->constrained('onus')->nullOnDelete();
            $table->enum('severity', ['critical','major','minor','warning','info'])->default('info');
            $table->text('message');
            $table->boolean('active')->default(true);
            $table->timestamp('detected_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('alarms');
    }
};
