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
        Schema::dropIfExists('settings');

        // Create assets table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('agenda_provider');
            $table->tinyInteger('active')->default(0);
            
            $table->timestamps();
        });
        
        Schema::dropIfExists('settings_agenda');

        // Create assets table
        Schema::create('settings_agenda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('monday')->default(0);
            $table->tinyInteger('tuesday')->default(0);
            $table->tinyInteger('wednesday')->default(0);
            $table->tinyInteger('thursday')->default(0);
            $table->tinyInteger('friday')->default(0);
            $table->tinyInteger('saturday')->default(0);
            $table->tinyInteger('sunday')->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        // Drop all the tables in reverse order
        Schema::dropIfExists('settings_agenda');
        Schema::dropIfExists('settings');
    }
};
