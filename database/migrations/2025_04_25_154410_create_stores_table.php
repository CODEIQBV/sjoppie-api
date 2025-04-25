<?php

use App\Models\Store;
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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('support_email');
            $table->string('vat_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->default('EUR');
            $table->string('timezone')->default('UTC');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create default store
        Store::create([
            'name' => 'My Store',
            'support_email' => 'support@example.com',
            'is_active' => true,
            'currency' => 'EUR',
            'timezone' => 'UTC',
            'settings' => [
                'maintenance_mode' => false,
                'allow_guest_checkout' => true,
                'min_order_amount' => 0,
                'tax_included' => false,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
