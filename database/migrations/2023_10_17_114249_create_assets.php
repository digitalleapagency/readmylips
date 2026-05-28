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
        Schema::dropIfExists('asset_details');
        Schema::dropIfExists('asset_category');
        Schema::dropIfExists('asset_tag');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('language_customers');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories_customers');
        Schema::dropIfExists('categories');
		Schema::dropIfExists('asset_types');
        
        // Create asset_types table
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            $table->timestamps();
        });
        
        Schema::create('asset_details', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('asset_type_id');
            
            $table->timestamps();
            $table->foreign('asset_type_id')->references('id')->on('asset_types');
        });

        // Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('image')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->tinyInteger('active')->nullable()->default(1);
            
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('categories');
        });
        
        Schema::create('category_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('category_id');
            
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        // Create tags table
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            $table->timestamps();
        });

        // Create languages table
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('language_code');
            
            $table->timestamps();
        });

        // Create customer languages table
        Schema::create('language_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('language_id');
            
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('language_id')->references('id')->on('languages');
        });

        // Create assets table
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle');
            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('asset_type_id');
            $table->unsignedBigInteger('customer_id');
            
            $table->timestamps();

            $table->foreign('asset_type_id')->references('id')->on('asset_types');
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        // Pivot table for tags and assets
        Schema::create('asset_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('tag_id');
            
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        // Pivot table for categories and assets
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('category_id');
            
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        // Create asset_details table
        Schema::create('asset_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('language_id');
            $table->text('detail')->nullable();
            
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    public function down()
    {
        // Drop all the tables in reverse order
        Schema::dropIfExists('asset_details');
        Schema::dropIfExists('asset_category');
        Schema::dropIfExists('asset_tag');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('language_customers');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories_customers');
        Schema::dropIfExists('categories');
		Schema::dropIfExists('asset_types');
    }
};
