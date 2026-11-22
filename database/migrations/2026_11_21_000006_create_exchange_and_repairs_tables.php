<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('old_brand');
            $table->string('old_model');
            $table->string('imei1', 25);
            $table->string('imei2', 25)->nullable();
            $table->string('serial_number', 50)->nullable();
            $table->string('condition_screen')->default('Good'); // Flawless, Minor Scratches, Cracked
            $table->string('condition_battery')->default('Good'); // Healthy, Moderate, Poor
            $table->string('condition_body')->default('Good'); // Like New, Scratches, Dented
            $table->string('functional_status')->default('Fully Functional'); // Fully Functional, Minor Issues, Major Defect
            $table->string('accessories_received')->nullable(); // Box, Charger, Bill, None
            $table->decimal('estimated_value', 12, 2)->default(0);
            $table->decimal('approved_value', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('approved');
            $table->timestamps();

            $table->index(['tenant_id', 'imei1']);
        });

        Schema::create('used_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('exchange_id')->nullable()->constrained('exchange_transactions')->nullOnDelete();
            $table->string('brand');
            $table->string('model');
            $table->string('imei1', 25);
            $table->string('imei2', 25)->nullable();
            $table->enum('grading', ['A_flawless', 'B_good', 'C_fair', 'defective'])->default('B_good');
            $table->decimal('purchase_cost', 12, 2)->default(0);
            $table->decimal('repair_cost', 12, 2)->default(0);
            $table->decimal('expected_selling_price', 12, 2)->default(0);
            $table->decimal('sold_price', 12, 2)->nullable();
            $table->enum('status', ['in_stock', 'under_inspection', 'refurbished', 'sold', 'scrapped'])->default('in_stock');
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'imei1']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('job_number');
            $table->string('brand');
            $table->string('model');
            $table->string('imei', 25)->nullable();
            $table->string('serial_number', 50)->nullable();
            $table->string('device_condition')->nullable(); // scratches, dents, water damage
            $table->text('reported_problem');
            $table->text('inspection_notes')->nullable();
            $table->string('accessories_received')->nullable(); // SIM tray, Memory Card, Cover
            $table->enum('warranty_status', ['out_of_warranty', 'under_store_warranty', 'under_brand_warranty'])->default('out_of_warranty');
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('estimated_cost', 12, 2)->default(0);
            $table->decimal('final_cost', 12, 2)->default(0);
            $table->decimal('advance_paid', 12, 2)->default(0);
            $table->enum('status', [
                'received',
                'under_inspection',
                'estimate_pending',
                'approval_pending',
                'approved',
                'parts_required',
                'under_repair',
                'testing',
                'ready',
                'delivered',
                'cancelled'
            ])->default('received');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'job_number']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('repair_job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained('repair_jobs')->cascadeOnDelete();
            $table->enum('type', ['part', 'labor'])->default('labor');
            $table->string('description');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete(); // if spare part consumed
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('repair_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained('repair_jobs')->cascadeOnDelete();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_status_histories');
        Schema::dropIfExists('repair_job_items');
        Schema::dropIfExists('repair_jobs');
        Schema::dropIfExists('used_devices');
        Schema::dropIfExists('exchange_transactions');
    }
};
