<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_settings', 'refused_reason_options')) {
                $table->json('refused_reason_options')->nullable();
            }
            if (!Schema::hasColumn('customer_settings', 'source_options')) {
                $table->json('source_options')->nullable();
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'customer_refused_reason_id')) {
                $table->string('customer_refused_reason_id', 64)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('customer_settings', function (Blueprint $table) {
            if (Schema::hasColumn('customer_settings', 'refused_reason_options')) {
                $table->dropColumn('refused_reason_options');
            }
            if (Schema::hasColumn('customer_settings', 'source_options')) {
                $table->dropColumn('source_options');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'customer_refused_reason_id')) {
                $table->dropColumn('customer_refused_reason_id');
            }
        });
    }
};
