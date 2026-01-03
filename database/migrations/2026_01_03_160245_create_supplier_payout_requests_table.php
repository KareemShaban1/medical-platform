<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payout_method');
            $table->text('payout_details');
            $table->text('supplier_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->foreignId('paid_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['supplier_id', 'status']);
        });

        // Pivot table for linking payout requests to order_suppliers
        Schema::create('supplier_payout_request_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_payout_request_id');
            // Explicitly name the foreign key to avoid "Identifier name too long" error (max 64 chars)
            $table->foreign('supplier_payout_request_id', 'spr_orders_request_id_fk')
                ->references('id')->on('supplier_payout_requests')
                ->cascadeOnDelete();

            $table->foreignId('order_supplier_id')->constrained('order_suppliers')->cascadeOnDelete();
            $table->decimal('amount', 12, 2); // Amount from this order included in the payout
            $table->timestamps();

            $table->unique(['supplier_payout_request_id', 'order_supplier_id'], 'payout_order_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payout_request_orders');
        Schema::dropIfExists('supplier_payout_requests');
    }
};
