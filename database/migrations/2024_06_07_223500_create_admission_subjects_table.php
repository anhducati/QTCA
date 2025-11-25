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
        Schema::create('admission_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('major_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->timestamps();

            // Tạo khóa ngoại
            $table->foreign('major_id')->references('id')->on('majors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_subjects');
    }
};
