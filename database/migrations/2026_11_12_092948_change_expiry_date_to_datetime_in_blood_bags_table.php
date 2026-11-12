<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_bags', function (Blueprint $table) {
            $table->dateTime('expiry_date')->change();
        });
    }

    public function down(): void
    {
        Schema::table('blood_bags', function (Blueprint $table) {
            $table->date('expiry_date')->change();
        });
    }
};