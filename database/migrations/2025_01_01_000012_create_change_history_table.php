<?php
// database/migrations/2025_01_01_000012_create_change_history_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('change_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->enum('device_type', ['OLT','ONU','ROUTER','SWITCH','SERVER']);
            $table->string('device_name', 100)->nullable();
            $table->text('command')->nullable();
            $table->text('result')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('date')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('change_history');
    }
};
