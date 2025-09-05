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
        Schema::create('billing_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\User::class);
            $table->integer('year');
            $table->integer('seq');
            $table->date('slip_date');
            $table->string('classification');
            $table->string('client_name')->nullable();
            $table->string('address')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->integer('discount')->nullable()->default(0);
            $table->unsignedBigInteger('total_price')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_slips');
    }
};
