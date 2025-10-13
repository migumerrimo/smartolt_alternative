<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Información específica del cliente
            $table->enum('customer_type', ['residential', 'business', 'corporate'])->default('residential');
            $table->text('address');
            $table->string('document_number', 50)->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->decimal('monthly_cost', 10, 2)->default(0.00);
            $table->json('extra_services')->nullable();
            
            // Datos adicionales
            $table->string('installation_contact')->nullable();
            $table->string('billing_contact')->nullable();
            $table->date('service_start_date')->nullable();
            $table->text('special_notes')->nullable();
            
            $table->timestamps();
            
            // Índices para mejor performance
            $table->index('user_id');
            $table->index('customer_type');
            $table->index('document_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};