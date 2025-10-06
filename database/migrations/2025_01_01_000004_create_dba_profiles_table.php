<?php
// database/migrations/2025_01_01_000004_create_dba_profiles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dba_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_id')->constrained('olts')->cascadeOnDelete();
            $table->string('name', 50);
            $table->enum('type', ['type1','type2','type3','type4']);
            $table->integer('max_bandwidth');
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('dba_profiles');
    }
};
