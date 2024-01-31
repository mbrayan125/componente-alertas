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
        Schema::create('process_instances_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('process_instance_id');
            $table->unsignedBigInteger('process_element_id');
            $table->unsignedBigInteger('history_previous_id')->nullable();
            $table->timestamps();

            $table->foreign('process_instance_id')->on('process_instances')->references('id');
            $table->foreign('process_element_id')->on('process_elements')->references('id');
            $table->foreign('history_previous_id')->on('process_instances_history')->references('id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_instances_history');
    }
};
