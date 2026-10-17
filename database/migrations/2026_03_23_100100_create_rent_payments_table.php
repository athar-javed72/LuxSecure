<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenancy_id')->constrained()->cascadeOnDelete();
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('method', 64)->nullable();
            $table->string('status', 32)->default('pending'); // pending, paid, overdue, waived
            $table->string('receipt_no', 64)->nullable()->unique();
            $table->timestamps();

            $table->index(['tenancy_id', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
    }
};
