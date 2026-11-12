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
        Schema::table('blood_bags', function (Blueprint $table) {
            $table->foreignId('refrigerator_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_bags', function (Blueprint $table) {
            $table->dropConstrainedForeignId('refrigerator_id');
        });
    }
};
