<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('immunization_records', function (Blueprint $table) {
            $table->text('vaccine_complaint')->nullable();
            $table->text('doctor_response')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('immunization_records', function (Blueprint $table) {
            $table->dropColumn(['vaccine_complaint', 'doctor_response']);
        });
    }
};
