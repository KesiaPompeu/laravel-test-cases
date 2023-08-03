<?php

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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('merchant_id');
            // TODO: Replace me with a brief explanation of why floats aren't the correct data type, and replace with the correct data type.
            //Explanation Floats are not the best data type for representing monetary values like commission rates due to potential precision issues.
            //For representing monetary values like commission rates, it is better to use a fixed-point data type with a specific precision
            //The decimal data type allows you to specify the total number of digits and the number of digits to the right of the decimal point,
            // ensuring accurate representation of decimal values without any rounding issues.
            $table->decimal('commission_rate',8,2);
            $table->string('discount_code');
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
        Schema::dropIfExists('affiliates');
    }
};
