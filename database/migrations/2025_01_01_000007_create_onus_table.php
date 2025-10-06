<?php
// database/migrations/2025_01_01_000007_create_onus_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('onus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->string('serial_number', 50)->unique();
            $table->string('model', 50)->nullable();
            $table->string('pon_port', 20);
            $table->foreignId('line_profile_id')->nullable()->constrained('line_profiles')->nullOnDelete();
            $table->foreignId('service_profile_id')->nullable()->constrained('service_profiles')->nullOnDelete();
            $table->enum('status', ['registered','authenticated','online','down'])->default('registered');
            $table->timestamp('last_contact')->nullable();
            $table->timestamp('registered_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('onus');
    }
};
