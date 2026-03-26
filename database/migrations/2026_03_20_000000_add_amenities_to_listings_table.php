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
        Schema::table('listings', function (Blueprint $table) {
            $table->decimal('deposit', 10, 2)->after('price')->nullable();
            $table->integer('area_sqft')->after('bathrooms')->nullable();
            $table->boolean('furnished')->after('is_available')->default(false);
            $table->boolean('wifi')->after('furnished')->default(false);
            $table->boolean('parking')->after('wifi')->default(false);
            $table->boolean('security')->after('parking')->default(false);
            $table->boolean('pool')->after('security')->default(false);
            $table->boolean('gym')->after('pool')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'deposit',
                'area_sqft',
                'furnished',
                'wifi',
                'parking',
                'security',
                'pool',
                'gym'
            ]);
        });
    }
};
