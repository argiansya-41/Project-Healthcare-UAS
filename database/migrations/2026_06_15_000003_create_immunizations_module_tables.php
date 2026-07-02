<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 255);
            $table->string('nik', 16)->unique()->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->date('date_of_birth');
            $table->string('place_of_birth', 100)->nullable();
            $table->decimal('birth_weight', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('immunization_vaccines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->integer('target_age_months');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('immunization_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->foreignId('vaccine_id')->constrained('immunization_vaccines')->onDelete('cascade');
            $table->foreignId('officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['scheduled', 'completed', 'missed'])->default('scheduled');
            $table->date('scheduled_date');
            $table->date('administered_date')->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('immunization_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained('immunization_records')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->date('send_date');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->enum('channel', ['dashboard', 'whatsapp', 'email'])->default('dashboard');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immunization_reminders');
        Schema::dropIfExists('immunization_records');
        Schema::dropIfExists('immunization_vaccines');
        Schema::dropIfExists('children');
    }
};
