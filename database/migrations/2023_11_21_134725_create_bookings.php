<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('bookings');

        // Create assets table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('calendar_type')->default(1);
            $table->string('calendar_id', 255)->nullable();
            $table->text('mollie_feedback')->nullable();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('booking_type');
            $table->unsignedBigInteger('price');
            $table->tinyInteger('paid')->default(0);
            
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    public function down()
    {
        // Drop all the tables in reverse order
        Schema::dropIfExists('bookings');
    }
};
