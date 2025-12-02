<?php

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

            // SSH
            $table->string('ssh_username', 100)->nullable();
            $table->string('ssh_password', 255)->nullable();
            $table->integer('ssh_port')->default(22);
            $table->boolean('ssh_active')->default(1);

            // Configuración del conector
            $table->string('connector_type', 100)->default('huawei_olt');

            // Campos de monitoreo
            $table->timestamp('last_connection_at')->nullable();
            $table->string('last_connection_status', 50)->nullable();
            $table->text('last_error')->nullable();

            // Tiempos
            $table->integer('connection_timeout')->default(5);
            $table->integer('command_timeout')->default(10);

            // Auto monitoreo
            $table->boolean('auto_monitoring')->default(0);

            $table->enum('status', ['active','inactive'])->default('active');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('olts');
    }
};
