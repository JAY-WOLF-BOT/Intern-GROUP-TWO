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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->string('photo_path');
            $table->string('photo_url');
            $table->integer('order')->default(1);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('listing_id');
            $table->unique(['listing_id', 'order']);
            
            // Constraint: max 3 photos per listing (enforced via application logic and check constraint)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
