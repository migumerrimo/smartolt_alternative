<?php
// database/migrations/2025_01_01_000008_create_traffic_table_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('traffic_table', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->string('name', 50);
            $table->integer('cir');
            $table->integer('pir');
            $table->integer('priority')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('traffic_table');
    }
};
