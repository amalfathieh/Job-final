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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("title");
            $table->text("body");
            $table->string("location");
            $table->enum('job_type', ['full_time', 'part_time', 'contract', 'temporary', 'volunteer']);
            $table->enum('work_place_type', ['on_site', 'hybrid', 'remote']);
            $table->integer('job_hours');
            $table->text('qualifications');
            $table->text('skills_req');
            $table->float('salary');
            $table->boolean('vacant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
