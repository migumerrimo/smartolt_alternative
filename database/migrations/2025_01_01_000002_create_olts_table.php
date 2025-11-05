<?php
// database/migrations/2025_01_01_000002_create_olts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('olts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('model', 50)->nullable();
            $table->enum('vendor', ['Huawei','ZTE','FiberHome','Other'])->default('Huawei');
            $table->string('management_ip', 50)->unique();
            $table->string('location', 255)->nullable();
            $table->string('firmware', 50)->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('olts');
    }
};
