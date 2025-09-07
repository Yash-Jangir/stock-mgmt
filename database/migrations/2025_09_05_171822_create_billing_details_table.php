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
        Schema::create('billing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\User::class);
            $table->foreignIdFor(App\Models\BillingSlip::class);
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('sku_id')->nullable();
            $table->unsignedInteger('qty')->nullable();
            $table->unsignedInteger('unit_price')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedInteger('unit_discount')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_details');
    }
};
