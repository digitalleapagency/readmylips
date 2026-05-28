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
        Schema::dropIfExists('customer_settings');

        // Create assets table
        Schema::create('customer_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('google')->default(1);
            $table->tinyInteger('vectera')->default(0);
            $table->unsignedBigInteger('booking_flow');
            
            $table->timestamps();
        });
        
        Schema::dropIfExists('vouchers');

        // Create assets table
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('value');
            $table->tinyInteger('active')->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        // Drop all the tables in reverse order
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('customer_settings');
    }
};
