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
        Schema::create('process_elements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('process_id');
            $table->unsignedBigInteger('user_role_id');
            $table->string('name');
            $table->string('type');
            $table->string('subtype')->nullable();
            $table->timestamps();

            $table->foreign('process_id')->on('processes')->references('id');
            $table->foreign('user_role_id')->on('user_roles')->references('id');
        });

        Schema::create('process_elements_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('process_element_id');
            $table->unsignedBigInteger('referenced_process_element_id');
            $table->string('direction');
            $table->string('name');

            $table->foreign('process_element_id')->references('id')->on('process_elements');
            $table->foreign('referenced_process_element_id')->references('id')->on('process_elements');

            $table->primary(['process_element_id', 'referenced_process_element_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_elements_relations');
        Schema::dropIfExists('process_elements');
    }
};
