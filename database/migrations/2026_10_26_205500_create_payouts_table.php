<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff_profiles')->nullOnDelete();
            $table->string('type')->default('admin_shop'); // admin_shop or staff_commission
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2);
            $table->string('payout_method')->default('gcash'); // gcash, maya, bank
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name')->nullable();
            $table->string('status')->default('completed'); // pending, processing, completed, failed
            $table->string('paymongo_payout_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
