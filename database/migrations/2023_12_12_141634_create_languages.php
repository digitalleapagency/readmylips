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
        Schema::dropIfExists('asset_languages');

        // Create assets table
        Schema::create('asset_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id')->nullable()->default(null);
            $table->integer('language_id')->nullable()->default(1);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        // Drop all the tables in reverse order
        Schema::dropIfExists('asset_languages');
    }
};
