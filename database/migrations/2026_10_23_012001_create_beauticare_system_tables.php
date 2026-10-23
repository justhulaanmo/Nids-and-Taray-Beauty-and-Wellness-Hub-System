<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add role & phone to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('staff')->after('email'); // admin, cashier, staff
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('phone');
            }
        });

        // 1. Staff Profiles (Tracks availability, specialization, and queue order)
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('phone')->nullable();
            $table->string('specialization')->default('all_around'); // manicurist, massage_therapist, all_around
            $table->string('status')->default('available'); // available, serving_in_shop, on_home_service, on_break, off_duty
            $table->integer('queue_position')->default(1);
            $table->decimal('commission_rate', 5, 2)->default(40.00); // 40% default commission
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_status_change')->nullable();
            $table->timestamps();
        });

        // 2. Service Categories (Nails, Massage, Packages)
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Services (Manicure, Pedicure, Cutics, Swedish Massage, etc.)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('estimated_duration_minutes')->default(45);
            $table->boolean('allow_home_service')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Transactions (Orders/Visits)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique(); // e.g. NT-20261023-001
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('service_type')->default('in_shop'); // in_shop, home_service
            $table->text('home_service_address')->nullable();
            $table->decimal('home_service_fee', 10, 2)->default(0.00);
            
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            
            $table->string('payment_method')->default('cash'); // cash, paymongo_qr, paymongo_link
            $table->string('payment_status')->default('pending'); // pending, paid, refunded
            $table->string('payment_reference')->nullable();
            $table->string('paymongo_checkout_id')->nullable();
            $table->text('paymongo_checkout_url')->nullable();
            
            $table->string('status')->default('in_progress'); // queued, in_progress, completed, cancelled
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 5. Transaction Items (Assigns specific staff to each service rendered)
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff_profiles')->nullOnDelete();
            
            $table->decimal('price', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(40.00);
            $table->decimal('staff_commission_amount', 10, 2)->default(0.00);
            
            $table->string('status')->default('in_progress'); // pending, in_progress, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
        Schema::dropIfExists('staff_profiles');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'is_active']);
        });
    }
};
