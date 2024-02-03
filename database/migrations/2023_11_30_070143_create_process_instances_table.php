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
        Schema::create('process_instances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_system_id');
            $table->unsignedBigInteger('process_id');
            $table->unsignedBigInteger('current_history_id')->nullable();
            $table->string('token');
            $table->timestamps();

            $table->foreign('target_system_id')->on('target_systems')->references('id');
            $table->foreign('process_id')->on('processes')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_instances');
    }
};
