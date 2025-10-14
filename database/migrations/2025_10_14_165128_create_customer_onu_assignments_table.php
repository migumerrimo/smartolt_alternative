<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_onu_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('onu_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('assignment_date')->useCurrent();
            $table->decimal('monthly_cost', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'suspended', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('customer_id');
            $table->index('onu_id');
            $table->index('status');
            $table->unique(['onu_id', 'status']); // Una ONU solo puede estar activa en una asignación
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_onu_assignments');
    }
};