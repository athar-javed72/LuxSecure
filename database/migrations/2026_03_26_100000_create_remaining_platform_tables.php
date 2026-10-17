<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->timestamp('kyc_verified_at')->nullable()->after('phone_verified_at');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->string('floor_plan_path')->nullable()->after('featured_image');
            $table->string('video_url')->nullable()->after('floor_plan_path');
            $table->string('private_document_path')->nullable()->after('video_url');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('payable');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('PKR');
            $table->string('gateway', 40); // bank_transfer, jazzcash, easypaisa
            $table->string('status', 32)->default('pending'); // pending, awaiting_approval, paid, failed, refunded
            $table->string('external_id')->nullable()->index();
            $table->string('idempotency_key')->nullable()->unique();
            $table->string('proof_path')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['gateway', 'status']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->string('pdf_path')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->json('line_items')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 40);
            $table->string('event_id')->index();
            $table->json('payload')->nullable();
            $table->string('status', 32)->default('processed');
            $table->timestamps();

            $table->unique(['gateway', 'event_id']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('body')->nullable();
            $table->string('status', 32)->default('pending'); // pending, approved, rejected
            $table->timestamps();

            $table->unique(['listing_id', 'user_id']);
            $table->index(['status', 'rating']);
        });

        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40); // cnic, license, registry
            $table->string('file_path');
            $table->string('status', 32)->default('pending'); // pending, approved, rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('phone_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 40);
            $table->string('code_hash');
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'phone']);
        });

        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 40); // sms, whatsapp
            $table->string('to');
            $table->text('body');
            $table->string('status', 32)->default('logged');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('message_logs');
        Schema::dropIfExists('phone_otps');
        Schema::dropIfExists('kyc_documents');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payment_webhook_events');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['floor_plan_path', 'video_url', 'private_document_path']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_verified_at', 'kyc_verified_at']);
        });
    }
};
