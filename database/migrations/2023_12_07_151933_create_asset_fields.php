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
        Schema::dropIfExists('asset_fields');

        // Create assets table
        Schema::create('asset_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('field_name', 255)->nullable();
            $table->tinyInteger('editable')->default(1);
            $table->tinyInteger('active')->default(0);
            
            $table->timestamps();
        });
        
        Schema::dropIfExists('asset_fields_info');

        // Create assets table
        Schema::create('asset_fields_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_field_id');
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('user_id');
            $table->string('field_value', 255)->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        // Drop all the tables in reverse order
        Schema::dropIfExists('asset_fields');
        Schema::dropIfExists('asset_fields_info');
    }
};
