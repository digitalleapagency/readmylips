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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('mail_name');
            $table->string('mail_subject');
            $table->text('mail_text');
            $table->unsignedBigInteger('customer_id');
            $table->timestamps();

            // Assuming you have a 'customers' table and 'id' is the primary key.
            // Adjust the table name and column if your setup is different.
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mails');
    }
};
