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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('customer_id');
            $table->foreignId('payment_gateway_id')->constrained()->cascadeOnDelete();
            $table->string('gateway_payment_id')->nullable()->comment('Payment ID from the gateway (e.g. Mollie)');
            $table->string('status')->default('pending')->comment('pending, paid, failed, cancelled, expired');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('description');
            $table->string('redirect_url');
            $table->string('webhook_url');
            $table->json('metadata')->nullable();
            $table->json('gateway_response')->nullable()->comment('Full response from the gateway');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->index(['status', 'created_at']);
            $table->index('gateway_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
