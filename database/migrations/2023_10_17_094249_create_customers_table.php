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
        Schema::dropIfExists('customers');
		
        Schema::create('customers', function (Blueprint $table) {
	        $table->id();
	        $table->string('name');
	        $table->string('email')->unique();
	        $table->string('domain')->unique();
	        $table->timestamps();
	    });
	
	    Schema::table('users', function (Blueprint $table) {
	        $table->unsignedBigInteger('customer_id')->nullable();
	        $table->foreign('customer_id')->references('id')->on('customers');
	    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
