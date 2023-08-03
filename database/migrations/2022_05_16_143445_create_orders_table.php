<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained();
            $table->foreignId('affiliate_id')->nullable()->constrained();
            // TODO: Replace floats with the correct data types (very similar to affiliates table)
            //Explanation Floats are not the best data type for representing monetary values like commission rates due to potential precision issues.
            //For representing monetary values like commission rates, it is better to use a fixed-point data type with a specific precision
            //The decimal data type allows you to specify the total number of digits and the number of digits to the right of the decimal point,
            // ensuring accurate representation of decimal values without any rounding issues.
            $table->decimal('subtotal',8,2);
            $table->decimal('commission_owed',8,2)->default(0.00);
            $table->string('payout_status')->default(Order::STATUS_UNPAID);
            $table->string('discount_code')->nullable();
            $table->string('external_order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
