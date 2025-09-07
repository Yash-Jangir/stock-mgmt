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
        Schema::create('stock_trasactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\User::class);
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
            $table->integer('stock_qty');
            $table->enum('type', array_column(App\Enums\TransactionType::cases(), 'value'));
            $table->unsignedInteger('price')->default(0)->nullable();
            $table->unsignedInteger('dis_price')->default(0)->nullable();
            $table->unsignedInteger('discount')->default(0)->nullable();
            $table->unsignedInteger('bill_id')->nullable();
            $table->unsignedInteger('bill_detail_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_trasactions');
    }
};
