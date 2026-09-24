<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); $table->foreignId('client_id')->constrained()->cascadeOnDelete(); $table->string('invoice_number')->unique();
            $table->date('issue_date'); $table->date('due_date')->nullable(); $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0); $table->decimal('tax_amount', 12, 2)->default(0); $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0); $table->decimal('amount_paid', 12, 2)->default(0); $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('invoice_id')->constrained()->cascadeOnDelete(); $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1); $table->decimal('unit_price', 12, 2)->default(0); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('invoice_items'); Schema::dropIfExists('invoices'); }
};
