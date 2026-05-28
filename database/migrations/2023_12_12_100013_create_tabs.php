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
        Schema::dropIfExists('asset_tabs');

        // Create assets table
        Schema::create('asset_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('tab_name');
            $table->string('tab_icon_code');
            $table->tinyInteger('active')->default(1);
            
            $table->timestamps();
        });
	
	    Schema::table('assets', function (Blueprint $table) {
	        $table->unsignedBigInteger('assets_tab_id')->nullable();
	    });
    }

    public function down()
    {
        // Drop all the tables in reverse order
        Schema::dropIfExists('asset_tabs');
    }
};
